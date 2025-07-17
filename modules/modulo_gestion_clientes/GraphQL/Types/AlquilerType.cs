using GestionClientes.Models;
using HotChocolate.Types;

namespace GestionClientes.GraphQL.Types
{
    public class AlquilerType : ObjectType<HistorialAlquiler>
    {
        protected override void Configure(IObjectTypeDescriptor<HistorialAlquiler> descriptor)
        {
            descriptor.Field(h => h.IdHistorial).Type<IdType>();
            descriptor.Field(h => h.ClienteId).Type<IntType>();
            descriptor.Field(h => h.VehiculoId).Type<IntType>();
            descriptor.Field(h => h.FechaInicio).Type<DateTimeType>();
            descriptor.Field(h => h.FechaFin).Type<DateTimeType>();
            descriptor.Field(h => h.TotalPagado).Type<DecimalType>();
        }
    }
}