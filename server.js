const express = require('express');
const http = require('http');
const { Server } = require('socket.io');

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
    cors: { origin: '*' }
});

io.on('connection', (socket) => {
    
    socket.on('registrar_usuario', (usuarioId) => {
        socket.join(`usuario_${usuarioId}`);
    });

    socket.on('enviar_mensagem', (data) => {
        if (data.destinatario_id) {
            data.id_mensagem = data.id_mensagem || data.id;
            io.to(`usuario_${data.destinatario_id}`).emit('receber_mensagem', data);
        }
    });

    socket.on('notificar_nova_conversa', (data) => {
        io.to(`usuario_${data.destinatario_id}`).emit('atualizar_sidebar_conversas', data);
    });

    socket.on('deletar_mensagem', (data) => {
        if (data.destinatario_id) {
            io.to(`usuario_${data.destinatario_id}`).emit('mensagem_deletada', {
                id_mensagem: data.id_mensagem
            });
        }
    });

});

server.listen(3000, () => {
    console.log('Servidor WebSocket rodando na porta 3000');
});