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

    socket.on('enviar_mensagem', (dados) => {
        io.to(`usuario_${dados.destinatario_id}`).emit('receber_mensagem', dados);
    });

    socket.on('notificar_nova_conversa', (dados) => {
        io.to(`usuario_${dados.destinatario_id}`).emit('atualizar_sidebar_conversas', dados);
    });

    // socket.on('deletar_mensagem', (data) => {
    //     io.to(`conversa_${data.id_conversa}`).emit('mensagem_deletada', {
    //         id_mensagem: data.id_mensagem
    //     });
    // });

});

server.listen(3000, () => {
    console.log('Servidor WebSocket rodando na porta 3000');
});