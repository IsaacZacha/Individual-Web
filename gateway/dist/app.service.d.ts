import { WebSocketClientService } from "./websocket-client.service";
export declare class GatewayService {
    private readonly webSocketClientService;
    constructor(webSocketClientService: WebSocketClientService);
    getHello(): string;
    getStatus(): {
        status: string;
        services: {};
        websocket: {
            target: string;
            event: string;
            connected: boolean;
            socketId: string;
        };
        timestamp: string;
    };
    testWebSocketConnection(): Promise<{
        success: boolean;
        message: string;
        error?: undefined;
    } | {
        success: boolean;
        error: any;
        message?: undefined;
    }>;
}
