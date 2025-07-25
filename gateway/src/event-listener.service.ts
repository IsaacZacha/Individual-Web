import { Injectable, type OnModuleInit } from "@nestjs/common"
import { WebSocketClientService } from "./websocket-client.service"  // <-- quita 'type' aquí

@Injectable()
export class EventListenerService implements OnModuleInit {
  constructor(private readonly webSocketClientService: WebSocketClientService) {}

  onModuleInit() {
    console.log("EventListenerService initialized - Ready to listen for GraphQL operations")
  }

  async sendResultToWebSocket(responseData: any) {
    try {
      console.log("Sending GraphQL response data to WebSocket:", {
        timestamp: new Date().toISOString(),
        dataKeys: Object.keys(responseData || {}),
      })

      await this.webSocketClientService.sendResultadoIaAdded(responseData)
    } catch (error) {
      console.error("Error sending result to WebSocket:", error)
    }
  }
}
