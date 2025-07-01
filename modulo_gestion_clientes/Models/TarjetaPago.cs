namespace GestionClientes.Models
{
    public class TarjetaPago
    {
        public int IdTarjeta { get; set; }
        public int ClienteId { get; set; }
        public string? NumeroTarjeta { get; set; }
        public string? Tipo { get; set; } // Visa, MasterCard, etc.
        public DateTime FechaExpiracion { get; set; }
        
        public Cliente? Cliente { get; set; }
    }
}