import { Controller, Get, Post } from "@nestjs/common"
import { GatewayService } from "./app.service"

@Controller()
export class AppController {
  constructor(private readonly gatewayService: GatewayService) {}

  @Get()
  getHello(): string {
    return this.gatewayService.getHello()
  }

  @Get("status")
  getStatus() {
    return this.gatewayService.getStatus()
  }

  @Post("test-websocket")
  async testWebSocket() {
    return this.gatewayService.testWebSocketConnection()
  }
}
