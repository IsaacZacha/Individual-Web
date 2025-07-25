package com.asw.modulo_administracion_vehiculos.service;

import io.nats.client.Connection;
import io.nats.client.Dispatcher;
import io.nats.client.MessageHandler;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

@Service
public class NatsService {

    private final Connection natsConnection;

    @Autowired
    public NatsService(Connection natsConnection) {
        this.natsConnection = natsConnection;
    }

    public void publishEvent(String subject, String message) {
        natsConnection.publish(subject, message.getBytes());
    }

    public Dispatcher subscribe(String subject, MessageHandler handler) {
        Dispatcher dispatcher = natsConnection.createDispatcher();
        dispatcher.subscribe(subject, handler);
        return dispatcher;
    }
}