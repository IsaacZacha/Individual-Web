namespace GestionClientes.Models
{
    public class EvaluacionCliente
    {
        public int IdEvaluacion { get; set; }
        public int ClienteId { get; set; }
        public DateTime Fecha { get; set; }
        public int Puntaje { get; set; } // 1-5 o 1-10 según necesidad
        public string? Comentarios { get; set; }
        
        public Cliente? Cliente { get; set; }
    }
}