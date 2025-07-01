namespace GestionClientes.Models
{
    public class HistorialAlquiler
    {
        public int IdHistorial { get; set; }
        public int ClienteId { get; set; }
        public int VehiculoId { get; set; }
        public DateTime FechaInicio { get; set; }
        public DateTime FechaFin { get; set; }
        public decimal TotalPagado { get; set; }
        
        public Cliente? Cliente { get; set; }
    }
}