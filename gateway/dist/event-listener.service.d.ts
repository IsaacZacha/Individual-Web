import { type OnModuleInit } from "@nestjs/common";
import { WebSocketClientService } from "./websocket-client.service";
export declare class EventListenerService implements OnModuleInit {
    private readonly webSocketClientService;
    constructor(webSocketClientService: WebSocketClientService);
    onModuleInit(): void;
    sendResultToWebSocket(responseData: any): Promise<void>;
}
