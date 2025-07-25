"use client"
import { ApolloProvider } from "@apollo/client"
import { client } from "@/lib/apollo-client"
import { Dashboard } from "@/components/dashboard"

export default function Home() {
  return (
    <ApolloProvider client={client}>
      <div className="min-h-screen bg-background">
        <Dashboard />
      </div>
    </ApolloProvider>
  )
}
