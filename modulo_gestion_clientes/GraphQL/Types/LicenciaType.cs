using GestionClientes.Models;
using HotChocolate.Types;

namespace GestionClientes.GraphQL.Types
{
    public class LicenciaType : ObjectType<LicenciaConduccion>
    {
        protected override void Configure(IObjectTypeDescriptor<LicenciaConduccion> descriptor)
        {
            descriptor.Field(l => l.IdLicencia).Type<IdType>();
            descriptor.Field(l => l.Numero).Type<StringType>();
            descriptor.Field(l => l.PaisEmision).Type<StringType>();
            descriptor.Field(l => l.FechaEmision).Type<DateTimeType>();
            descriptor.Field(l => l.FechaVencimiento).Type<DateTimeType>();
            descriptor.Field(l => l.ClienteId).Type<IntType>();
        }
    }
}