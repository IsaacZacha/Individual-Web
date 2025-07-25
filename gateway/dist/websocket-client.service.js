"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.WebSocketClientService = void 0;
const common_1 = require("@nestjs/common");
const socket_io_client_1 = __importDefault(require("socket.io-client"));
let WebSocketClientService = class WebSocketClientService {
    socket;
    WEBSOCKET_URL = "http://localhost:3003";
    onModuleInit() {
        this.connectToWebSocketService();
    }
    onModuleDestroy() {
        if (this.socket) {
            this.socket.disconnect();
        }
    }
    isConnecting = false;
    connectToWebSocketService() {
        if (this.isConnecting || (this.socket && this.socket.connected))
            return;
        this.isConnecting = true;
        this.socket = (0, socket_io_client_1.default)(this.WEBSOCKET_URL, {
            transports: ["websocket"],
            autoConnect: true,
            reconnection: true,
            reconnectionDelay: 1000,
            reconnectionAttempts: 5,
        });
    }
    async sendResultadoIaAdded(responseData) {
        return new Promise((resolve, reject) => {
            if (!this.socket || !this.socket.connected) {
                console.warn("⚠️ WebSocket not connected, attempting to reconnect...");
                this.connectToWebSocketService();
                setTimeout(() => {
                    if (this.socket?.connected) {
                        this.emitResultadoIaAdded(responseData, resolve, reject);
                    }
                    else {
                        reject(new Error("WebSocket connection failed"));
                    }
                }, 1000);
                return;
            }
            this.emitResultadoIaAdded(responseData, resolve, reject);
        });
    }
    emitResultadoIaAdded(responseData, resolve, reject) {
        try {
            this.socket.emit("resultadoIaAdded", responseData, (acknowledgment) => {
                if (acknowledgment?.success !== false) {
                    console.log("📤 resultadoIaAdded sent successfully to WebSocket service");
                    resolve();
                }
                else {
                    console.error("❌ Failed to send resultadoIaAdded to WebSocket service");
                    reject(new Error("Failed to send resultadoIaAdded"));
                }
            });
        }
        catch (error) {
            console.error("Error emitting resultadoIaAdded:", error);
            reject(error);
        }
    }
    isConnected() {
        return this.socket?.connected || false;
    }
    getConnectionStatus() {
        return {
            connected: this.isConnected(),
            url: this.WEBSOCKET_URL,
            socketId: this.socket?.id,
            timestamp: new Date().toISOString(),
        };
    }
};
exports.WebSocketClientService = WebSocketClientService;
exports.WebSocketClientService = WebSocketClientService = __decorate([
    (0, common_1.Injectable)()
], WebSocketClientService);
//# sourceMappingURL=websocket-client.service.js.map