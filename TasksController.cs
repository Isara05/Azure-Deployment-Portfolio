using Microsoft.AspNetCore.Mvc;
using IsaraTaskManagerWeb.Models;

namespace IsaraTaskManagerWeb.Controllers
{
    public class TasksController : Controller
    {
        // Temporary in-memory task storage
        private static List<TaskItem> tasks = new List<TaskItem>();

        public IActionResult Index()
        {
            // Read environment value from Azure App Service
            // If running locally, display "Local Development"
            ViewBag.Environment =
                Environment.GetEnvironmentVariable("TaskManager__Environment")
                ?? "Local Development";

            return View(tasks);
        }

        [HttpPost]
        public IActionResult Add(string taskName, string priority)
        {
            if (!string.IsNullOrWhiteSpace(taskName))
            {
                TaskItem newTask = new TaskItem
                {
                    Id = tasks.Count + 1,
                    TaskName = taskName,
                    Priority = priority,
                    IsCompleted = false
                };

                tasks.Add(newTask);
            }

            return RedirectToAction("Index");
        }

        [HttpPost]
        public IActionResult Complete(int id)
        {
            var task = tasks.FirstOrDefault(t => t.Id == id);

            if (task != null)
            {
                task.IsCompleted = true;
            }

            return RedirectToAction("Index");
        }

        [HttpPost]
        public IActionResult Delete(int id)
        {
            var task = tasks.FirstOrDefault(t => t.Id == id);

            if (task != null)
            {
                tasks.Remove(task);
            }

            return RedirectToAction("Index");
        }
    }
}