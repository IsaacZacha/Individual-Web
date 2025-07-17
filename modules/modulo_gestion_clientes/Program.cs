using GestionClientes.Data;
using GestionClientes.GraphQL;
using Microsoft.EntityFrameworkCore;

var builder = WebApplication.CreateBuilder(args);

// Configuración de la base de datos
builder.Services.AddPooledDbContextFactory<AppDbContext>(options =>
    options.UseNpgsql(builder.Configuration.GetConnectionString("PostgreSQL")));

// Configuración de GraphQL
builder.Services
    .AddGraphQLServer()
    .AddQueryType<Query>()
    .AddMutationType<Mutation>()
    .AddProjections()
    .AddFiltering()
    .AddSorting()
    .ModifyRequestOptions(
        opt =>
        {
            opt.IncludeExceptionDetails = true;
        }

        )

    .RegisterDbContext<AppDbContext>(DbContextKind.Pooled);

var app = builder.Build();

// Aplicar migraciones automáticamente al iniciar
// using (var scope = app.Services.CreateScope())
// {
//     var services = scope.ServiceProvider;
//     var dbContext = services.GetRequiredService<AppDbContext>();
//     dbContext.Database.Migrate();
// }
app.MapGraphQL();


app.Run();