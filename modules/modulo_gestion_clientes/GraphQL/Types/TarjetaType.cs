using GestionClientes.Models;
using HotChocolate.Types;

namespace GestionClientes.GraphQL.Types
{
    public class TarjetaType : ObjectType<TarjetaPago>
    {
        protected override void Configure(IObjectTypeDescriptor<TarjetaPago> descriptor)
        {
            descriptor.Field(t => t.IdTarjeta).Type<IdType>();
            descriptor.Field(t => t.NumeroTarjeta).Type<StringType>();
            descriptor.Field(t => t.Tipo).Type<StringType>();
            descriptor.Field(t => t.FechaExpiracion).Type<DateTimeType>();
            descriptor.Field(t => t.ClienteId).Type<IntType>();
        }
    }
}