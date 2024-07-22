const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const mysql = require('mysql2');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

app.use(express.static('public'));
app.use('/uploads', express.static('uploads'));

// Configurar multer para conservar el nombre y la extensión del archivo
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, 'uploads/');
    },
    filename: (req, file, cb) => {
        cb(null, file.originalname);
    }
});
const upload = multer({ storage: storage });

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'muro_virtual'
});

db.connect(err => {
    if (err) throw err;
    console.log('Conectado a la base de datos.');
});

app.get('/muros/:muroId', (req, res) => {
    const { muroId } = req.params;
    const sql = 'SELECT titulo, descripcion FROM muros WHERE muro_id = ?';
    db.query(sql, [muroId], (err, results) => {
        if (err) throw err;
        if (results.length > 0) {
            res.json(results[0]);
        } else {
            res.status(404).send('Muro no encontrado');
        }
    });
});

app.get('/publicaciones/:muroId', (req, res) => {
    const { muroId } = req.params;
    const sql = 'SELECT publi_id, rotulo, contenido, archivo FROM publicaciones WHERE muro_id = ?';
    db.query(sql, [muroId], (err, results) => {
        if (err) throw err;
        res.json(results);
    });
});

app.post('/nuevaPublicacion', upload.single('archivo'), (req, res) => {
    const { rotulo, contenido, muroId } = req.body;
    const archivo = req.file ? req.file.originalname : null;
    const sql = 'INSERT INTO publicaciones (muro_id, rotulo, contenido, archivo) VALUES (?, ?, ?, ?)';
    db.query(sql, [muroId, rotulo, contenido, archivo], (err, result) => {
        if (err) throw err;
        io.to(muroId).emit('publicacionCreada', { publi_id: result.insertId, rotulo, contenido, archivo });
        res.sendStatus(200);
    });
});

app.post('/editarPublicacion', upload.single('archivo'), (req, res) => {
    const { rotulo, contenido, publiId, muroId } = req.body;
    const archivo = req.file ? req.file.originalname : null;
    let sql = 'UPDATE publicaciones SET rotulo = ?, contenido = ?';
    const params = [rotulo, contenido];
    if (archivo) {
        sql += ', archivo = ?';
        params.push(archivo);
    }
    sql += ' WHERE publi_id = ?';
    params.push(publiId);

    db.query(sql, params, (err, result) => {
        if (err) throw err;
        io.to(muroId).emit('publicacionEditada', { publi_id: publiId, rotulo, contenido, archivo });
        res.sendStatus(200);
    });
});

io.on('connection', socket => {
    socket.on('joinMuro', muroId => {
        socket.join(muroId);
    });

    socket.on('eliminarPublicacion', data => {
        const { publiId, muroId } = data;
        const sql = 'SELECT archivo FROM publicaciones WHERE publi_id = ?';
        db.query(sql, [publiId], (err, results) => {
            if (err) throw err;
            const archivo = results[0].archivo;
            db.query('DELETE FROM publicaciones WHERE publi_id = ?', [publiId], err => {
                if (err) throw err;
                if (archivo) {
                    fs.unlink(path.join(__dirname, 'uploads', archivo), err => {
                        if (err) throw err;
                    });
                }
                io.to(muroId).emit('publicacionEliminada', { publi_id: publiId });
            });
        });
    });
});

server.listen(3000, () => {
    console.log('Servidor corriendo en http://localhost:3000');
});