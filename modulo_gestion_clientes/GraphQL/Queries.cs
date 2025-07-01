using GestionClientes.Data;
using GestionClientes.Models;
using HotChocolate;
using HotChocolate.Data;
using HotChocolate.Types;
using Microsoft.EntityFrameworkCore;
using System.Linq;
using System.Threading.Tasks;

namespace GestionClientes.GraphQL
{
    public class Query
    {
        [UseDbContext(typeof(AppDbContext))]
        [UseProjection]
        [UseFiltering]
        [UseSorting]
        public IQueryable<Cliente> GetClientes([ScopedService] AppDbContext context)
        {
            try
            {
                return context.Clientes;
            }
            catch (Exception ex)
            {
                throw new GraphQLException(
                    ErrorBuilder.New()
                        .SetMessage("Error al obtener clientes")
                        .SetCode("CLIENTES_ERROR")
                        .SetException(ex)
                        .Build());
            }
        }

        [UseDbContext(typeof(AppDbContext))]
        [UseProjection]
        [UseFiltering]
        [UseSorting]
        public IQueryable<LicenciaConduccion> GetLicencias([ScopedService] AppDbContext context)
        {
            try
            {
                return context.LicenciasConduccion;
            }
            catch (Exception ex)
            {
                throw new GraphQLException(
                    ErrorBuilder.New()
                        .SetMessage("Error al obtener licencias")
                        .SetCode("LICENCIAS_ERROR")
                        .SetException(ex)
                        .Build());
            }
        }

        [UseDbContext(typeof(AppDbContext))]
        [UseProjection]
        [UseFiltering]
        [UseSorting]
        public IQueryable<TarjetaPago> GetTarjetas([ScopedService] AppDbContext context)
        {
            try
            {
                return context.TarjetasPago;
            }
            catch (Exception ex)
            {
                throw new GraphQLException(
                    ErrorBuilder.New()
                        .SetMessage("Error al obtener tarjetas de pago")
                        .SetCode("TARJETAS_ERROR")
                        .SetException(ex)
                        .Build());
            }
        }

        [UseDbContext(typeof(AppDbContext))]
        [UseProjection]
        [UseFiltering]
        [UseSorting]
        public IQueryable<EvaluacionCliente> GetEvaluaciones([ScopedService] AppDbContext context)
        {
            try
            {
                return context.EvaluacionesCliente;
            }
            catch (Exception ex)
            {
                throw new GraphQLException(
                    ErrorBuilder.New()
                        .SetMessage("Error al obtener evaluaciones")
                        .SetCode("EVALUACIONES_ERROR")
                        .SetException(ex)
                        .Build());
            }
        }

        [UseDbContext(typeof(AppDbContext))]
        [UseProjection]
        [UseFiltering]
        [UseSorting]
        public IQueryable<HistorialAlquiler> GetAlquileres([ScopedService] AppDbContext context)
        {
            try
            {
                return context.HistorialAlquileres;
            }
            catch (Exception ex)
            {
                throw new GraphQLException(
                    ErrorBuilder.New()
                        .SetMessage("Error al obtener historial de alquileres")
                        .SetCode("ALQUILERES_ERROR")
                        .SetException(ex)
                        .Build());
            }
        }

        public async Task<Cliente?> GetClienteById(
            [ScopedService] AppDbContext context, 
            int id)
        {
            try
            {
                return await context.Clientes
                    .FirstOrDefaultAsync(c => c.IdCliente == id);
            }
            catch (Exception ex)
            {
                throw new GraphQLException(
                    ErrorBuilder.New()
                        .SetMessage($"Error al obtener cliente con ID {id}")
                        .SetCode("CLIENTE_NOT_FOUND")
                        .SetException(ex)
                        .Build());
            }
        }
    }
}