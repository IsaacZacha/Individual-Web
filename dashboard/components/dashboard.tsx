"use client"

import { useState, useEffect } from "react"
import { useLazyQuery } from "@apollo/client"
import { io, type Socket } from "socket.io-client"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Textarea } from "@/components/ui/textarea"
import { ScrollArea } from "@/components/ui/scroll-area"
import { Separator } from "@/components/ui/separator"
import { Alert, AlertDescription } from "@/components/ui/alert"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Activity, Database, Play, Wifi, WifiOff, MessageSquare, Users, Car, Building, CreditCard } from "lucide-react"
import { SAMPLE_QUERIES } from "@/lib/queries"

interface Message {
  id: string
  type: "query" | "mutation" | "connection" | "error"
  content: string
  timestamp: Date
  query?: string
  variables?: any
}

export function Dashboard() {
  const [socket, setSocket] = useState<Socket | null>(null)
  const [isConnected, setIsConnected] = useState(false)
  const [messages, setMessages] = useState<Message[]>([])
  const [customQuery, setCustomQuery] = useState("")
  const [selectedQuery, setSelectedQuery] = useState("")
  const [queryResult, setQueryResult] = useState<any>(null)
  const [isLoading, setIsLoading] = useState(false)

  // Lazy query para ejecutar queries dinámicamente
  const [executeQuery] = useLazyQuery(SAMPLE_QUERIES.GET_ALL_CLIENTES, {
    onCompleted: (data) => {
      setQueryResult(data)
      setIsLoading(false)

      // Emitir mensaje via WebSocket
      if (socket && isConnected) {
        socket.emit("graphql_query_executed", {
          query: "GET_ALL_CLIENTES",
          result: data,
          timestamp: new Date().toISOString(),
        })
      }
    },
    onError: (error) => {
      setIsLoading(false)
      addMessage("error", `Error ejecutando query: ${error.message}`)
    },
  })

  // Conectar/desconectar WebSocket
  const toggleConnection = () => {
    if (isConnected && socket) {
      socket.disconnect()
      setSocket(null)
      setIsConnected(false)
      addMessage("connection", "Desconectado del servidor WebSocket")
    } else {
      const newSocket = io("http://localhost:3001", {
        transports: ["websocket"],
      })

      newSocket.on("connect", () => {
        setIsConnected(true)
        addMessage("connection", "Conectado al servidor WebSocket")
      })

      newSocket.on("disconnect", () => {
        setIsConnected(false)
        addMessage("connection", "Desconectado del servidor WebSocket")
      })

      newSocket.on("query_notification", (data) => {
        addMessage("query", `Query ejecutada: ${data.query}`, data.query, data.variables)
      })

      newSocket.on("mutation_notification", (data) => {
        addMessage("mutation", `Mutación ejecutada: ${data.mutation}`, data.mutation, data.variables)
      })

      newSocket.on("error", (error) => {
        addMessage("error", `Error WebSocket: ${error.message}`)
      })

      setSocket(newSocket)
    }
  }

  const addMessage = (type: Message["type"], content: string, query?: string, variables?: any) => {
    const newMessage: Message = {
      id: Date.now().toString(),
      type,
      content,
      timestamp: new Date(),
      query,
      variables,
    }
    setMessages((prev) => [newMessage, ...prev])
  }

  const executeSelectedQuery = () => {
    if (!selectedQuery) return

    setIsLoading(true)

    switch (selectedQuery) {
      case "clientes":
        executeQuery({ query: SAMPLE_QUERIES.GET_ALL_CLIENTES })
        break
      case "vehiculos":
        executeQuery({ query: SAMPLE_QUERIES.GET_ALL_VEHICULOS })
        break
      case "empleados":
        executeQuery({ query: SAMPLE_QUERIES.GET_ALL_EMPLEADOS })
        break
      case "reservas":
        executeQuery({ query: SAMPLE_QUERIES.GET_ALL_RESERVAS })
        break
      default:
        setIsLoading(false)
    }
  }

  const executeCustomQuery = () => {
    if (!customQuery.trim()) return

    setIsLoading(true)
    addMessage("query", `Ejecutando query personalizada...`, customQuery)

    // Simular ejecución de query personalizada
    setTimeout(() => {
      setIsLoading(false)
      setQueryResult({ message: "Query personalizada ejecutada (simulación)" })

      if (socket && isConnected) {
        socket.emit("custom_query_executed", {
          query: customQuery,
          timestamp: new Date().toISOString(),
        })
      }
    }, 1000)
  }

  const getMessageIcon = (type: Message["type"]) => {
    switch (type) {
      case "query":
        return <Database className="h-4 w-4 text-blue-500" />
      case "mutation":
        return <Play className="h-4 w-4 text-green-500" />
      case "connection":
        return <Activity className="h-4 w-4 text-purple-500" />
      case "error":
        return <MessageSquare className="h-4 w-4 text-red-500" />
      default:
        return <MessageSquare className="h-4 w-4" />
    }
  }

  const getMessageBadgeColor = (type: Message["type"]) => {
    switch (type) {
      case "query":
        return "bg-blue-100 text-blue-800"
      case "mutation":
        return "bg-green-100 text-green-800"
      case "connection":
        return "bg-purple-100 text-purple-800"
      case "error":
        return "bg-red-100 text-red-800"
      default:
        return "bg-gray-100 text-gray-800"
    }
  }

  useEffect(() => {
    return () => {
      if (socket) {
        socket.disconnect()
      }
    }
  }, [socket])

  return (
    <div className="container mx-auto p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Dashboard GraphQL + WebSocket</h1>
          <p className="text-muted-foreground">Sistema de gestión de alquileres con comunicación en tiempo real</p>
        </div>

        <div className="flex items-center gap-4">
          <Badge variant={isConnected ? "default" : "secondary"} className="flex items-center gap-2">
            {isConnected ? <Wifi className="h-4 w-4" /> : <WifiOff className="h-4 w-4" />}
            {isConnected ? "Conectado" : "Desconectado"}
          </Badge>

          <Button onClick={toggleConnection} variant={isConnected ? "destructive" : "default"}>
            {isConnected ? "Desconectar" : "Conectar WebSocket"}
          </Button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Panel de Queries */}
        <div className="lg:col-span-2 space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Database className="h-5 w-5" />
                Ejecutor de Queries GraphQL
              </CardTitle>
              <CardDescription>Selecciona una query predefinida o escribe una personalizada</CardDescription>
            </CardHeader>
            <CardContent>
              <Tabs defaultValue="predefined" className="w-full">
                <TabsList className="grid w-full grid-cols-2">
                  <TabsTrigger value="predefined">Queries Predefinidas</TabsTrigger>
                  <TabsTrigger value="custom">Query Personalizada</TabsTrigger>
                </TabsList>

                <TabsContent value="predefined" className="space-y-4">
                  <div className="flex gap-4">
                    <Select value={selectedQuery} onValueChange={setSelectedQuery}>
                      <SelectTrigger className="flex-1">
                        <SelectValue placeholder="Selecciona una query" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="clientes">
                          <div className="flex items-center gap-2">
                            <Users className="h-4 w-4" />
                            Obtener todos los clientes
                          </div>
                        </SelectItem>
                        <SelectItem value="vehiculos">
                          <div className="flex items-center gap-2">
                            <Car className="h-4 w-4" />
                            Obtener todos los vehículos
                          </div>
                        </SelectItem>
                        <SelectItem value="empleados">
                          <div className="flex items-center gap-2">
                            <Building className="h-4 w-4" />
                            Obtener todos los empleados
                          </div>
                        </SelectItem>
                        <SelectItem value="reservas">
                          <div className="flex items-center gap-2">
                            <CreditCard className="h-4 w-4" />
                            Obtener todas las reservas
                          </div>
                        </SelectItem>
                      </SelectContent>
                    </Select>

                    <Button onClick={executeSelectedQuery} disabled={!selectedQuery || isLoading}>
                      {isLoading ? "Ejecutando..." : "Ejecutar"}
                    </Button>
                  </div>
                </TabsContent>

                <TabsContent value="custom" className="space-y-4">
                  <Textarea
                    placeholder="Escribe tu query GraphQL aquí..."
                    value={customQuery}
                    onChange={(e) => setCustomQuery(e.target.value)}
                    className="min-h-[120px] font-mono"
                  />
                  <Button onClick={executeCustomQuery} disabled={!customQuery.trim() || isLoading} className="w-full">
                    {isLoading ? "Ejecutando..." : "Ejecutar Query Personalizada"}
                  </Button>
                </TabsContent>
              </Tabs>
            </CardContent>
          </Card>

          {/* Resultados */}
          {queryResult && (
            <Card>
              <CardHeader>
                <CardTitle>Resultado de la Query</CardTitle>
              </CardHeader>
              <CardContent>
                <ScrollArea className="h-[300px] w-full">
                  <pre className="text-sm bg-muted p-4 rounded-md overflow-auto">
                    {JSON.stringify(queryResult, null, 2)}
                  </pre>
                </ScrollArea>
              </CardContent>
            </Card>
          )}
        </div>

        {/* Panel de Mensajes WebSocket */}
        <div className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <MessageSquare className="h-5 w-5" />
                Mensajes en Tiempo Real
              </CardTitle>
              <CardDescription>Mensajes del servidor WebSocket</CardDescription>
            </CardHeader>
            <CardContent>
              {!isConnected && (
                <Alert>
                  <WifiOff className="h-4 w-4" />
                  <AlertDescription>Conecta al servidor WebSocket para ver mensajes en tiempo real</AlertDescription>
                </Alert>
              )}

              <ScrollArea className="h-[500px] w-full">
                <div className="space-y-3">
                  {messages.length === 0 ? (
                    <p className="text-muted-foreground text-center py-8">No hay mensajes aún</p>
                  ) : (
                    messages.map((message, index) => (
                      <div key={message.id}>
                        <div className="flex items-start gap-3 p-3 rounded-lg bg-muted/50">
                          {getMessageIcon(message.type)}
                          <div className="flex-1 space-y-1">
                            <div className="flex items-center gap-2">
                              <Badge variant="secondary" className={getMessageBadgeColor(message.type)}>
                                {message.type}
                              </Badge>
                              <span className="text-xs text-muted-foreground">
                                {message.timestamp.toLocaleTimeString()}
                              </span>
                            </div>
                            <p className="text-sm">{message.content}</p>
                            {message.query && (
                              <pre className="text-xs bg-background p-2 rounded border overflow-auto">
                                {message.query}
                              </pre>
                            )}
                          </div>
                        </div>
                        {index < messages.length - 1 && <Separator className="my-2" />}
                      </div>
                    ))
                  )}
                </div>
              </ScrollArea>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  )
}
