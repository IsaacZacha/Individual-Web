import { type OnModuleInit, type OnModuleDestroy } from "@nestjs/common";
export declare class WebSocketClientService implements OnModuleInit, OnModuleDestroy {
    private socket;
    private readonly WEBSOCKET_URL;
    onModuleInit(): void;
    onModuleDestroy(): void;
    private isConnecting;
    private connectToWebSocketService;
    sendResultadoIaAdded(responseData: any): Promise<void>;
    private emitResultadoIaAdded;
    isConnected(): boolean;
    getConnectionStatus(): {
        connected: boolean;
        url: string;
        socketId: string;
        timestamp: string;
    };
}
