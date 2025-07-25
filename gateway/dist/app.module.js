"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.AppModule = void 0;
const common_1 = require("@nestjs/common");
const graphql_1 = require("@nestjs/graphql");
const apollo_1 = require("@nestjs/apollo");
const fs_1 = require("fs");
const default_1 = require("@apollo/server/plugin/landingPage/default");
const app_service_1 = require("./app.service");
const app_controller_1 = require("./app.controller");
const gateway_gateway_1 = require("./gateway.gateway");
const event_listener_service_1 = require("./event-listener.service");
const websocket_client_service_1 = require("./websocket-client.service");
const supergraphSdl = (0, fs_1.readFileSync)('./supergraph.graphql', 'utf8');
let AppModule = class AppModule {
};
exports.AppModule = AppModule;
exports.AppModule = AppModule = __decorate([
    (0, common_1.Module)({
        imports: [
            graphql_1.GraphQLModule.forRoot({
                driver: apollo_1.ApolloGatewayDriver,
                gateway: {
                    supergraphSdl,
                },
                server: {
                    playground: false,
                    plugins: [(0, default_1.ApolloServerPluginLandingPageLocalDefault)()],
                    context: ({ req }) => ({
                        eventService: req.app?.get?.(event_listener_service_1.EventListenerService),
                    }),
                },
            }),
        ],
        controllers: [app_controller_1.AppController],
        providers: [app_service_1.GatewayService, gateway_gateway_1.GatewayGateway, event_listener_service_1.EventListenerService, websocket_client_service_1.WebSocketClientService],
    })
], AppModule);
//# sourceMappingURL=app.module.js.map