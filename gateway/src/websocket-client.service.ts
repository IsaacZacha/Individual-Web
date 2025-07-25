import { Injectable, type OnModuleInit, type OnModuleDestroy } from "@nestjs/common"
import { type Socket } from "socket.io-client"
import io from "socket.io-client"


@Injectable()
export class WebSocketClientService implements OnModuleInit, OnModuleDestroy {
  private socket: typeof Socket
  private readonly WEBSOCKET_URL = "http://localhost:3003"

  onModuleInit() {
    this.connectToWebSocketService()
  }

  onModuleDestroy() {
    if (this.socket) {
      this.socket.disconnect()
    }
  }
private isConnecting = false;

private connectToWebSocketService() {
  if (this.isConnecting || (this.socket && this.socket.connected)) return;
  this.isConnecting = true;

  this.socket = io(this.WEBSOCKET_URL, {
    transports: ["websocket"],
    autoConnect: true,
    reconnection: true,
    reconnectionDelay: 1000,
    reconnectionAttempts: 5,
  });

  // this.socket.on("connect", () => {
  //   this.isConnecting = false;
  //   console.log(`✅ Connected to WebSocket service at ${this.WEBSOCKET_URL}`);
  // });

  // this.socket.on("disconnect", (reason) => {
  //   console.log(`❌ Disconnected from WebSocket service. Reason: ${reason}`);
  // });

  // this.socket.on("connect_error", (error) => {
  //   this.isConnecting = false;
  //   console.error("🔴 WebSocket connection error:", error.message);
  // });

  // this.socket.on("reconnect", (attemptNumber) => {
  //   console.log(`🔄 Reconnected after ${attemptNumber} attempts`);
  // });

  // Otros listeners...
}


  async sendResultadoIaAdded(responseData: any): Promise<void> {
    return new Promise((resolve, reject) => {
      if (!this.socket || !this.socket.connected) {
        console.warn("⚠️ WebSocket not connected, attempting to reconnect...")
        this.connectToWebSocketService()

        // Esperar un momento para la reconexión
        setTimeout(() => {
          if (this.socket?.connected) {
            this.emitResultadoIaAdded(responseData, resolve, reject)
          } else {
            reject(new Error("WebSocket connection failed"))
          }
        }, 1000)
        return
      }

      this.emitResultadoIaAdded(responseData, resolve, reject)
    })
  }

  private emitResultadoIaAdded(responseData: any, resolve: () => void, reject: (error: Error) => void) {
    try {
      // Enviar el evento resultadoIaAdded con los datos del response
      this.socket.emit("resultadoIaAdded", responseData, (acknowledgment) => {
        if (acknowledgment?.success !== false) {
          console.log("📤 resultadoIaAdded sent successfully to WebSocket service")
          resolve()
        } else {
          console.error("❌ Failed to send resultadoIaAdded to WebSocket service")
          reject(new Error("Failed to send resultadoIaAdded"))
        }
      })
    } catch (error) {
      console.error("Error emitting resultadoIaAdded:", error)
      reject(error as Error)
    }
  }

  // Método para verificar el estado de la conexión
  isConnected(): boolean {
    return this.socket?.connected || false
  }

  // Método para obtener estadísticas de conexión
  getConnectionStatus() {
    return {
      connected: this.isConnected(),
      url: this.WEBSOCKET_URL,
      socketId: this.socket?.id,
      timestamp: new Date().toISOString(),
    }
  }
}
