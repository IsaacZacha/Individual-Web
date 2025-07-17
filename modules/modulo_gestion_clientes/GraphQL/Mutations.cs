using GestionClientes.Data;
using GestionClientes.Models;
using HotChocolate;
using Microsoft.EntityFrameworkCore;

namespace GestionClientes.GraphQL
{
    public class Mutation
    {
        public async Task<Cliente> AddCliente(
            [Service] AppDbContext context, 
            ClienteInput input)
        {
            var cliente = new Cliente
            {
                Nombre = input.Nombre,
                Apellido = input.Apellido,
                CedulaPasaporte = input.CedulaPasaporte,
                Direccion = input.Direccion,
                Correo = input.Correo,
                Telefono = input.Telefono
            };
            
            context.Clientes.Add(cliente);
            await context.SaveChangesAsync();
            return cliente;
        }
        
        public async Task<LicenciaConduccion> AddLicencia(
            [Service] AppDbContext context, 
            LicenciaInput input)
        {
            var licencia = new LicenciaConduccion
            {
                ClienteId = input.ClienteId,
                Numero = input.Numero,
                PaisEmision = input.PaisEmision,
                FechaEmision = input.FechaEmision,
                FechaVencimiento = input.FechaVencimiento
            };
            
            context.LicenciasConduccion.Add(licencia);
            await context.SaveChangesAsync();
            return licencia;
        }
        
        public async Task<TarjetaPago> AddTarjeta(
            [Service] AppDbContext context, 
            TarjetaInput input)
        {
            var tarjeta = new TarjetaPago
            {
                ClienteId = input.ClienteId,
                NumeroTarjeta = input.NumeroTarjeta,
                Tipo = input.Tipo,
                FechaExpiracion = input.FechaExpiracion
            };
            
            context.TarjetasPago.Add(tarjeta);
            await context.SaveChangesAsync();
            return tarjeta;
        }
        
        public async Task<EvaluacionCliente> AddEvaluacion(
            [Service] AppDbContext context, 
            EvaluacionInput input)
        {
            var evaluacion = new EvaluacionCliente
            {
                ClienteId = input.ClienteId,
                Fecha = DateTime.Now,
                Puntaje = input.Puntaje,
                Comentarios = input.Comentarios
            };
            
            context.EvaluacionesCliente.Add(evaluacion);
            await context.SaveChangesAsync();
            return evaluacion;
        }
        
        public async Task<HistorialAlquiler> AddAlquiler(
            [Service] AppDbContext context, 
            AlquilerInput input)
        {
            var alquiler = new HistorialAlquiler
            {
                ClienteId = input.ClienteId,
                VehiculoId = input.VehiculoId,
                FechaInicio = input.FechaInicio,
                FechaFin = input.FechaFin,
                TotalPagado = input.TotalPagado
            };
            
            context.HistorialAlquileres.Add(alquiler);
            await context.SaveChangesAsync();
            return alquiler;
        }
        
        public async Task<string> DeleteCliente(
            [Service] AppDbContext context, 
            int id)
        {
            var cliente = await context.Clientes.FindAsync(id);
            if (cliente == null)
                return "Cliente no encontrado";
                
            context.Clientes.Remove(cliente);
            await context.SaveChangesAsync();
            return "Cliente eliminado correctamente";
        }
    }

    // Input Types
    public record ClienteInput(
        string Nombre,
        string Apellido,
        string CedulaPasaporte,
        string Direccion,
        string Correo,
        string Telefono);
    
    public record LicenciaInput(
        int ClienteId,
        string Numero,
        string PaisEmision,
        DateTime FechaEmision,
        DateTime FechaVencimiento);
    
    public record TarjetaInput(
        int ClienteId,
        string NumeroTarjeta,
        string Tipo,
        DateTime FechaExpiracion);
    
    public record EvaluacionInput(
        int ClienteId,
        int Puntaje,
        string Comentarios);
    
    public record AlquilerInput(
        int ClienteId,
        int VehiculoId,
        DateTime FechaInicio,
        DateTime FechaFin,
        decimal TotalPagado);
}