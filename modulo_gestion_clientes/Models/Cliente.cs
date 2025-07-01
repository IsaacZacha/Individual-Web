namespace GestionClientes.Models
{
    public class Cliente
    {
        public int IdCliente { get; set; }
        public string? Nombre { get; set; }
        public string? Apellido { get; set; }
        public string? CedulaPasaporte { get; set; }
        public string? Direccion { get; set; }
        public string? Correo { get; set; }
        public string? Telefono { get; set; }
        
        public ICollection<LicenciaConduccion> LicenciasConduccion { get; set; } = new List<LicenciaConduccion>();
        public ICollection<TarjetaPago> TarjetasPago { get; set; } = new List<TarjetaPago>();
        public ICollection<EvaluacionCliente> Evaluaciones { get; set; } = new List<EvaluacionCliente>();
        public ICollection<HistorialAlquiler> HistorialAlquileres { get; set; } = new List<HistorialAlquiler>();
    }
}