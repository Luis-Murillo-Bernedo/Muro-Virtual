const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const mysql = require('mysql2');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'muro_virtual'
});

db.connect((err) => {
    if (err) {
        throw err;
    }
    console.log('Conectado a la base de datos MySQL');
});

app.use(express.static('public'));
app.use(express.json());

app.get('/muros/:muro_id', (req, res) => {
    const muroId = req.params.muro_id;
    const query = 'SELECT * FROM muros WHERE muro_id = ?';
    db.query(query, [muroId], (err, results) => {
        if (err) {
            return res.status(500).send(err);
        }
        res.json(results[0]);
    });
});

app.get('/publicaciones/:muro_id', (req, res) => {
    const muroId = req.params.muro_id;
    const query = 'SELECT * FROM publicaciones WHERE muro_id = ?';
    db.query(query, [muroId], (err, results) => {
        if (err) {
            return res.status(500).send(err);
        }
        res.json(results);
    });
});

io.on('connection', (socket) => {
    console.log('Nuevo cliente conectado');

    socket.on('joinMuro', (muroId) => {
        socket.join(muroId);
    });

    socket.on('nuevaPublicacion', (data) => {
        const query = 'INSERT INTO publicaciones (muro_id, rotulo, contenido) VALUES (?, ?, ?)';
        db.query(query, [data.muroId, data.rotulo, data.contenido], (err, result) => {
            if (err) {
                console.error(err);
                return;
            }
            io.to(data.muroId).emit('publicacionCreada', {
                publi_id: result.insertId,
                muro_id: data.muroId,
                rotulo: data.rotulo,
                contenido: data.contenido
            });
        });
    });

    socket.on('editarPublicacion', (data) => {
        const query = 'UPDATE publicaciones SET rotulo = ?, contenido = ? WHERE publi_id = ?';
        db.query(query, [data.rotulo, data.contenido, data.publiId], (err, result) => {
            if (err) {
                console.error(err);
                return;
            }
            io.to(data.muroId).emit('publicacionEditada', {
                publi_id: data.publiId,
                rotulo: data.rotulo,
                contenido: data.contenido
            });
        });
    });

    socket.on('eliminarPublicacion', (data) => {
        const query = 'DELETE FROM publicaciones WHERE publi_id = ?';
        db.query(query, [data.publiId], (err, result) => {
            if (err) {
                console.error(err);
                return;
            }
            io.to(data.muroId).emit('publicacionEliminada', {
                publi_id: data.publiId
            });
        });
    });

    socket.on('disconnect', () => {
        console.log('Cliente desconectado');
    });
});

server.listen(3000, () => {
    console.log('Servidor ejecutándose en http://localhost:3000');
});