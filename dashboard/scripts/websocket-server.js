const { createServer } = require("http")
const { Server } = require("socket.io")

const httpServer = createServer()
const io = new Server(httpServer, {
  cors: {
    origin: "http://localhost:3000",
    methods: ["GET", "POST"],
  },
})

io.on("connection", (socket) => {
  console.log("Cliente conectado:", socket.id)

  // Escuchar cuando se ejecuta una query GraphQL
  socket.on("graphql_query_executed", (data) => {
    console.log("Query GraphQL ejecutada:", data)

    // Emitir notificación a todos los clientes conectados
    io.emit("query_notification", {
      query: data.query,
      timestamp: data.timestamp,
      clientId: socket.id,
    })
  })

  // Escuchar queries personalizadas
  socket.on("custom_query_executed", (data) => {
    console.log("Query personalizada ejecutada:", data)

    io.emit("query_notification", {
      query: "CUSTOM_QUERY",
      timestamp: data.timestamp,
      clientId: socket.id,
      customQuery: data.query,
    })
  })

  // Simular notificaciones periódicas del sistema
  const interval = setInterval(() => {
    socket.emit("system_notification", {
      message: "Sistema funcionando correctamente",
      timestamp: new Date().toISOString(),
      type: "info",
    })
  }, 30000) // Cada 30 segundos

  socket.on("disconnect", () => {
    console.log("Cliente desconectado:", socket.id)
    clearInterval(interval)
  })
})

const PORT = process.env.PORT || 3001
httpServer.listen(PORT, () => {
  console.log(`Servidor WebSocket ejecutándose en puerto ${PORT}`)
})
