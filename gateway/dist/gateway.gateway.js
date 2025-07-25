"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.GatewayGateway = void 0;
const websockets_1 = require("@nestjs/websockets");
const common_1 = require("@nestjs/common");
let GatewayGateway = class GatewayGateway {
    server;
    handleConnection(client) {
        console.log(`🔗 Client connected to gateway: ${client.id}`);
    }
    handleDisconnect(client) {
        console.log(`🔌 Client disconnected from gateway: ${client.id}`);
    }
    emitLocalEvent(eventData) {
        this.server.emit("gateway-event", eventData);
    }
};
exports.GatewayGateway = GatewayGateway;
__decorate([
    (0, websockets_1.WebSocketServer)(),
    __metadata("design:type", Function)
], GatewayGateway.prototype, "server", void 0);
exports.GatewayGateway = GatewayGateway = __decorate([
    (0, common_1.Injectable)(),
    (0, websockets_1.WebSocketGateway)({
        cors: {
            origin: "*",
        },
    })
], GatewayGateway);
//# sourceMappingURL=gateway.gateway.js.map