import { Module } from '@nestjs/common';
import { GraphQLModule } from '@nestjs/graphql';
import { ApolloGatewayDriver, ApolloGatewayDriverConfig } from '@nestjs/apollo';
import { readFileSync } from 'fs';
import { ApolloServerPluginLandingPageLocalDefault } from '@apollo/server/plugin/landingPage/default';

import { GatewayService } from './app.service';
import { AppController } from './app.controller';
import { GatewayGateway } from './gateway.gateway';
import { EventListenerService } from './event-listener.service';
import { WebSocketClientService } from './websocket-client.service';

const supergraphSdl = readFileSync('./supergraph.graphql', 'utf8');

@Module({
  imports: [
    GraphQLModule.forRoot<ApolloGatewayDriverConfig>({
      driver: ApolloGatewayDriver,
      gateway: {
        supergraphSdl,  // PASA EL SDL COMO STRING AQUÍ
      },
      server: {
        playground: false,
        plugins: [ApolloServerPluginLandingPageLocalDefault()],
        context: ({ req }) => ({
          eventService: req.app?.get?.(EventListenerService),
        }),
      },
    }),
  ],
  controllers: [AppController],
  providers: [GatewayService, GatewayGateway, EventListenerService, WebSocketClientService],
})
export class AppModule {}
