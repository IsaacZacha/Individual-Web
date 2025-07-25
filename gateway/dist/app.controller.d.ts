import { GatewayService } from "./app.service";
export declare class AppController {
    private readonly gatewayService;
    constructor(gatewayService: GatewayService);
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
    testWebSocket(): Promise<{
        success: boolean;
        message: string;
        error?: undefined;
    } | {
        success: boolean;
        error: any;
        message?: undefined;
    }>;
}
