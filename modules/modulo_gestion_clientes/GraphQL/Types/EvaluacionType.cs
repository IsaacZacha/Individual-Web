using GestionClientes.Models;
using HotChocolate.Types;

namespace GestionClientes.GraphQL.Types
{
    public class EvaluacionType : ObjectType<EvaluacionCliente>
    {
        protected override void Configure(IObjectTypeDescriptor<EvaluacionCliente> descriptor)
        {
            descriptor.Field(e => e.IdEvaluacion).Type<IdType>();
            descriptor.Field(e => e.Fecha).Type<DateTimeType>();
            descriptor.Field(e => e.Puntaje).Type<IntType>();
            descriptor.Field(e => e.Comentarios).Type<StringType>();
            descriptor.Field(e => e.ClienteId).Type<IntType>();
        }
    }
}