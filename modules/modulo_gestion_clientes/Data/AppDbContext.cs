using Microsoft.EntityFrameworkCore;
using GestionClientes.Models;

namespace GestionClientes.Data
{
    public class AppDbContext : DbContext
    {
        public AppDbContext(DbContextOptions<AppDbContext> options) : base(options) { }

        public DbSet<Cliente> Clientes { get; set; }
        public DbSet<LicenciaConduccion> LicenciasConduccion { get; set; }
        public DbSet<TarjetaPago> TarjetasPago { get; set; }
        public DbSet<EvaluacionCliente> EvaluacionesCliente { get; set; }
        public DbSet<HistorialAlquiler> HistorialAlquileres { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            // Configuración de Cliente
            modelBuilder.Entity<Cliente>(entity =>
            {
                entity.HasKey(c => c.IdCliente);
                entity.Property(c => c.Nombre).IsRequired().HasMaxLength(100);
                entity.Property(c => c.Apellido).IsRequired().HasMaxLength(100);
                entity.Property(c => c.CedulaPasaporte).IsRequired().HasMaxLength(20);
                entity.HasIndex(c => c.CedulaPasaporte).IsUnique();
                entity.Property(c => c.Correo).HasMaxLength(100);
                entity.Property(c => c.Telefono).HasMaxLength(20);
            });

            // Configuración de LicenciaConduccion
            modelBuilder.Entity<LicenciaConduccion>(entity =>
            {
                entity.HasKey(l => l.IdLicencia);
                entity.Property(l => l.Numero).IsRequired().HasMaxLength(50);
                entity.Property(l => l.PaisEmision).IsRequired().HasMaxLength(50);
                
                entity.HasOne(l => l.Cliente)
                    .WithMany(c => c.LicenciasConduccion)
                    .HasForeignKey(l => l.ClienteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            // Configuración de TarjetaPago
            modelBuilder.Entity<TarjetaPago>(entity =>
            {
                entity.HasKey(t => t.IdTarjeta);
                entity.Property(t => t.NumeroTarjeta).IsRequired().HasMaxLength(20);
                entity.Property(t => t.Tipo).IsRequired().HasMaxLength(20);
                
                entity.HasOne(t => t.Cliente)
                    .WithMany(c => c.TarjetasPago)
                    .HasForeignKey(t => t.ClienteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            // Configuración de EvaluacionCliente
            modelBuilder.Entity<EvaluacionCliente>(entity =>
            {
                entity.HasKey(e => e.IdEvaluacion);
                entity.Property(e => e.Fecha).IsRequired();
                entity.Property(e => e.Puntaje).IsRequired();
                
                entity.HasOne(e => e.Cliente)
                    .WithMany(c => c.Evaluaciones)
                    .HasForeignKey(e => e.ClienteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });

            // Configuración de HistorialAlquiler
            modelBuilder.Entity<HistorialAlquiler>(entity =>
            {
                entity.HasKey(h => h.IdHistorial);
                entity.Property(h => h.FechaInicio).IsRequired();
                entity.Property(h => h.FechaFin).IsRequired();
                entity.Property(h => h.TotalPagado).IsRequired().HasColumnType("decimal(18,2)");
                
                entity.HasOne(h => h.Cliente)
                    .WithMany(c => c.HistorialAlquileres)
                    .HasForeignKey(h => h.ClienteId)
                    .OnDelete(DeleteBehavior.Cascade);
            });
        }
    }
}