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
exports.EventListenerService = void 0;
const common_1 = require("@nestjs/common");
const websocket_client_service_1 = require("./websocket-client.service");
let EventListenerService = class EventListenerService {
    webSocketClientService;
    constructor(webSocketClientService) {
        this.webSocketClientService = webSocketClientService;
    }
    onModuleInit() {
        console.log("EventListenerService initialized - Ready to listen for GraphQL operations");
    }
    async sendResultToWebSocket(responseData) {
        try {
            console.log("Sending GraphQL response data to WebSocket:", {
                timestamp: new Date().toISOString(),
                dataKeys: Object.keys(responseData || {}),
            });
            await this.webSocketClientService.sendResultadoIaAdded(responseData);
        }
        catch (error) {
            console.error("Error sending result to WebSocket:", error);
        }
    }
};
exports.EventListenerService = EventListenerService;
exports.EventListenerService = EventListenerService = __decorate([
    (0, common_1.Injectable)(),
    __metadata("design:paramtypes", [websocket_client_service_1.WebSocketClientService])
], EventListenerService);
//# sourceMappingURL=event-listener.service.js.map