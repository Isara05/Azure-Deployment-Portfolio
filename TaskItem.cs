namespace IsaraTaskManagerWeb.Models
{
    public class TaskItem
    {
        public int Id { get; set; }

        public string TaskName { get; set; } = "";

        public string Priority { get; set; } = "";

        public bool IsCompleted { get; set; }
    }
}