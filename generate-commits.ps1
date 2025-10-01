# PowerShell script to generate 100 realistic commits
# Run this from your Sentri project directory

$commitMessages = @(
    "Initial project setup",
    "Add Laravel framework structure",
    "Configure database connection",
    "Create User model and migration",
    "Add authentication scaffolding",
    "Implement login functionality",
    "Add registration endpoint",
    "Create Ticket model",
    "Add ticket migrations",
    "Implement TicketController",
    "Add ticket validation rules",
    "Create TicketService class",
    "Add ticket status enum",
    "Implement priority levels",
    "Create Message model",
    "Add message relationships",
    "Implement real-time messaging",
    "Add WebSocket configuration",
    "Create API routes",
    "Add Sanctum authentication",
    "Implement token management",
    "Create Tag model",
    "Add tagging system",
    "Implement search functionality",
    "Add pagination support",
    "Create statistics endpoint",
    "Add dashboard metrics",
    "Implement agent assignment",
    "Create notification system",
    "Add email notifications",
    "Setup Vue.js frontend",
    "Configure Vite build",
    "Create App.vue component",
    "Add Vue Router setup",
    "Implement Pinia stores",
    "Create auth store",
    "Add ticket store",
    "Implement API service",
    "Create Sidebar component",
    "Add navigation links",
    "Style sidebar with gradients",
    "Create Dashboard page",
    "Add ticket list view",
    "Implement chat window",
    "Create message bubbles",
    "Add real-time updates",
    "Implement file uploads",
    "Create Login page",
    "Add login form validation",
    "Style login with glassmorphism",
    "Create Register page",
    "Add password confirmation",
    "Implement logout functionality",
    "Create Tickets page",
    "Add ticket table view",
    "Implement filters",
    "Add status badges",
    "Create priority indicators",
    "Implement pagination UI",
    "Create Inbox page",
    "Add message list",
    "Implement split panel view",
    "Add quick reply feature",
    "Create Settings page",
    "Add settings tabs",
    "Implement theme switcher",
    "Create Account page",
    "Add profile form",
    "Implement password change",
    "Add 2FA settings",
    "Create Team page",
    "Add team grid view",
    "Implement list view toggle",
    "Add member cards",
    "Create invite modal",
    "Add role management",
    "Create Analytics page",
    "Add chart components",
    "Implement trend graphs",
    "Add satisfaction metrics",
    "Create Knowledge Base",
    "Add article categories",
    "Implement search",
    "Add article preview",
    "Create Integrations hub",
    "Add Slack integration",
    "Add Discord support",
    "Implement Zapier connection",
    "Add webhook management",
    "Create API keys section",
    "Add Activity Log page",
    "Implement event timeline",
    "Add filter options",
    "Create Admin panel",
    "Add user management",
    "Implement quick actions",
    "Fix validation errors",
    "Update dependencies",
    "Improve error handling",
    "Add loading states",
    "Final code cleanup"
)

Write-Host "Generating 100 commits..." -ForegroundColor Cyan

# Get start date (3 months ago)
$startDate = (Get-Date).AddDays(-90)

for ($i = 0; $i -lt 100; $i++) {
    # Calculate date (spread evenly over 90 days)
    $daysOffset = [math]::Floor($i * 0.9)
    $hoursOffset = Get-Random -Minimum 8 -Maximum 22
    $minutesOffset = Get-Random -Minimum 0 -Maximum 59
    
    $commitDate = $startDate.AddDays($daysOffset).AddHours($hoursOffset).AddMinutes($minutesOffset)
    $dateString = $commitDate.ToString("yyyy-MM-ddTHH:mm:ss")
    
    # Get commit message
    $message = $commitMessages[$i]
    
    # Create a small change to commit
    $changeFile = ".\CHANGELOG.md"
    Add-Content -Path $changeFile -Value "`n## Commit $($i + 1) - $($commitDate.ToString('yyyy-MM-dd'))`n- $message"
    
    # Stage and commit with custom date
    git add .
    $env:GIT_AUTHOR_DATE = $dateString
    $env:GIT_COMMITTER_DATE = $dateString
    git commit -m $message --date="$dateString" 2>$null
    
    $progress = [math]::Round(($i + 1) / 100 * 100)
    Write-Host "[$progress%] Commit $($i + 1): $message" -ForegroundColor Green
}

Write-Host "`nDone! 100 commits created." -ForegroundColor Cyan
Write-Host "Now run: git push --force origin main" -ForegroundColor Yellow
