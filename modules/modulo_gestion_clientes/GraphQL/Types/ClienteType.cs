using GestionClientes.Models;
using HotChocolate.Types;

namespace GestionClientes.GraphQL.Types
{
    public class ClienteType : ObjectType<Cliente>
    {
        protected override void Configure(IObjectTypeDescriptor<Cliente> descriptor)
        {
            descriptor.Field(c => c.IdCliente).Type<IdType>().Name("id");
            descriptor.Field(c => c.Nombre).Type<StringType>();
            descriptor.Field(c => c.Apellido).Type<StringType>();
            descriptor.Field(c => c.CedulaPasaporte).Type<StringType>();
            descriptor.Field(c => c.Direccion).Type<StringType>();
            descriptor.Field(c => c.Correo).Type<StringType>();
            descriptor.Field(c => c.Telefono).Type<StringType>();
            
            descriptor.Field(c => c.LicenciasConduccion)
                .Type<ListType<LicenciaType>>()
                .Name("licencias");
            
            descriptor.Field(c => c.TarjetasPago)
                .Type<ListType<TarjetaType>>()
                .Name("tarjetas");
            
            descriptor.Field(c => c.Evaluaciones)
                .Type<ListType<EvaluacionType>>()
                .Name("evaluaciones");
            
            descriptor.Field(c => c.HistorialAlquileres)
                .Type<ListType<AlquilerType>>()
                .Name("alquileres");
        }
    }
}