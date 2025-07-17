namespace GestionClientes.Models
{
    public class LicenciaConduccion
    {
        public int IdLicencia { get; set; }
        public int ClienteId { get; set; }
        public string? Numero { get; set; }
        public string? PaisEmision { get; set; }
        public DateTime? FechaEmision { get; set; }
        public DateTime? FechaVencimiento { get; set; }

        public Cliente? Cliente { get; set; }
    }
}