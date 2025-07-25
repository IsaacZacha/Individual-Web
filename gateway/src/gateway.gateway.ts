import {
  WebSocketGateway,
  WebSocketServer,
  type OnGatewayConnection,
  type OnGatewayDisconnect,
} from "@nestjs/websockets"
import type { Server, Socket } from "socket.io"
import { Injectable } from "@nestjs/common"

@Injectable()
@WebSocketGateway({
  cors: {
    origin: "*",
  },
})
export class GatewayGateway implements OnGatewayConnection, OnGatewayDisconnect {
  @WebSocketServer()
  server: Server

  handleConnection(client: Socket) {
    console.log(`🔗 Client connected to gateway: ${client.id}`)
  }

  handleDisconnect(client: Socket) {
    console.log(`🔌 Client disconnected from gateway: ${client.id}`)
  }

  // Método para emitir eventos locales si es necesario
  emitLocalEvent(eventData: any) {
    this.server.emit("gateway-event", eventData)
  }
}
