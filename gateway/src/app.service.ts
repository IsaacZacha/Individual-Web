import { Injectable } from "@nestjs/common"
import { WebSocketClientService } from "./websocket-client.service"

@Injectable()
export class GatewayService {
  constructor(private readonly webSocketClientService: WebSocketClientService) {}

  getHello(): string {
    return "GraphQL Gateway with WebSocket Event Forwarding is running!"
  }

  getStatus() {
    const wsStatus = this.webSocketClientService.getConnectionStatus()

    return {
      status: "active",
      services: {
        // citas: "http://localhost:2000/graphql",
        // psicodiagnostico: "http://localhost:3003/graphql",
      },
      websocket: {
        target: "http://localhost:3003",
        event: "resultadoIaAdded",
        connected: wsStatus.connected,
        socketId: wsStatus.socketId,
      },
      timestamp: new Date().toISOString(),
    }
  }

  async testWebSocketConnection() {
    try {
      const testData = {
        test: true,
        message: "Test connection from gateway",
        timestamp: new Date().toISOString(),
      }

      await this.webSocketClientService.sendResultadoIaAdded(testData)
      return { success: true, message: "Test event sent successfully" }
    } catch (error) {
      return { success: false, error: error.message }
    }
  }
}
