# AI Master Blueprint: Aether Catalyst Framework
**Version: 4.0 (The Ultimate Project Lawbook)**
**Last Updated: 2026-04-14**

---

## 🏛️ Framework Philosophy (The "Soul")
The Aether Catalyst framework is a minimalist, high-performance PHP MVC ecosystem designed for speed and explicit control. When working in this codebase or cloning it, adhere to these pillars:

1. **Explicit > Implicit**: No magic routing or hidden magic methods. Everything is explicitly loaded via `libs/load.php`.
2. **Performance First**: Favor standard PHP and SQL over heavy abstractions. Use PDO with prepared statements.
3. **Consistency**: Follow the established directory and naming patterns strictly. Do not "innovate" new structures.
4. **Modularity**: Business logic goes in Models (`libs/app/`), routing logic in APIs (`libs/api/`), and UI in Templates (`_templates/`).
5. **Separation**: Soul (Framework) vs. Body (Application). Never mix core engine code with project features.

---

## 🚀 Core Concepts & Patterns

### 1. Bootstrapping
Every public PHP entry point (index, admin, api) MUST start with:
```php
<?php
require_once 'libs/load.php'; 
// This loads Composer, aliases Aether\* classes, connects DB, and initiates Session.
```

### 2. 📋 Core Class Reference

| Class | Key Static Methods | Description |
|-------|--------------------|-------------|
| `Database` | `getConnection(): PDO`, `query($s, $p): DatabaseResult` | Core PDO connectivity. |
| `Session` | `isAuthenticated()`, `ensureLogin()`, `renderView($v, $d, $l)`, `get($k)`, `set($k, $v)` | Session facades & rendering. |
| `User` | `signup($u, $p, $e, $ph)`, `login($u, $p)`, `listAll($l, $o, $f)` | User ORM & Auth logic. |
| `UserSession` | `authenticate($u, $p, $fp)`, `authorize($token)`, `isValid()` | Login session management. |
| `API` | `processApi()`, `response($data, $code)`, `json($array)`, `addMiddleware($fn)` | REST Routing & Response. |

### 3. 🖼️ Template Engine & Layouts
- **View Rendering**: `Session::renderView('folder/view', $data, '_masterLayout')`
- **Partial Loading**: `load_template('partial_name')`
- **Directory Structure**:
  - `_templates/_master.php`: Public layout (GSAP + ToastV3).
  - `_templates/_masterForAdmin.php`: Admin layout (Sidebar + Sidebar Navigation).
  - `_templates/admin/`: Admin-specific view components.
  - `_templates/core/`: Head, Nav, Footer, and Error templates.
  - `index.php`: The main public landing template.

### 4. 🔌 API Endpoint Pattern
Routing is file-system based. `libs/api/{namespace}/{method}.php` maps to `POST /api/{namespace}/{method}`.

- **Rule**: Every API file must assign a closure to `${basename(__FILE__, '.php')}`.
- **Checklist**: 1. Auth check (`isAuthenticated`), 2. Param check (`paramsExists`), 3. Action, 4. Response.

### 5. 🛡️ Security & Standards
- **Config**: Always use `get_config('key')`. Never hardcode credentials.
- **SQL**: Always use prepared statements via `Database::query()` or `$conn->prepare()`.
- **Auth**: Use `Session::ensureLogin()` for pages and `$this->isAuthenticated()` for APIs.
- **Toasts**: Use `showToast(type, title, msg)` or `toast.success(title, msg)`.

---

## 🧬 Architectural DNA (Soul vs. Body)

### 1. The Immutable Soul (`libs/src/`)
**Core Framework (The Engine)**. Remains constant across projects. **Example: `Database.php`**
```php
namespace Aether;
use PDO;
class Database {
    public static $db = null;
    public static function getConnection(): PDO {
        if (self::$db === null) {
            self::$db = new PDO("mysql:host=".get_config('db_server').";dbname=".get_config('db_name'), get_config('db_username'), get_config('db_password'));
        }
        return self::$db;
    }
}
```

### 2. The Muscle & Heart (`libs/app/`)
**Application Layer (Project Content)**. Local to this website. **Example: `Ad.php`**
```php
namespace App;
use Aether\Traits\SQLGetterSetter;
class Ad {
    use SQLGetterSetter; // Automates CRUD properties
    public int $id;
    public string $table = 'ads';
    public function __construct(int $id) {
        $this->id = $id;
        $this->conn = \Aether\Database::getConnection();
    }
}
```

### 3. The Nervous System (`libs/api/`)
**Functional Bridges (Closures)**. **Example: `libs/api/ads/create.php`**
```php
<?php
use App\Ad;
${basename(__FILE__, '.php')} = function() {
    if (!$this->isAuthenticated()) { $this->response($this->json(['error'=>'Unauthorized']), 401); }
    if ($this->paramsExists(['name', 'type'])) {
        $success = Ad::create($this->_request);
        $this->response($this->json(['status'=>'success']), 200);
    }
};
```

---

## 🛠️ The Refactoring Blueprint: Before vs. After
Use this to transform a "Messy" feature into an "Aether-ized" one.

**Before (Messy):**
```php
// htdocs/messy_ad.php
include 'db_connect.php'; 
$res = mysqli_query($conn, "SELECT * FROM ads WHERE id = ".$_GET['id']);
$row = mysqli_fetch_assoc($res);
echo "<div>".$row['name']."</div>";
```

**After (Aether-ized):**
1. **Model (`libs/app/Ad.php`)**: Add project function to heart.
2. **Template (`_templates/ad/view.php`)**: Clean View.
3. **Controller (`admin.php`)**: `Session::renderView('ad/view', ['ad' => new App\Ad(ID)]);`

---

## 📂 Directory Blueprint (The "Body")
Strict structure to maintain or clone. **HTDOCS is the only public folder.**

```bash
/ (Root)
├── composer.json        # PHP Dependencies (Aether\ -> libs/src, App\ -> libs/app)
├── .env                 # Secrets
├── tests/               # Quality: Unit (Models) & Integration (APIs)
├── project/             # Developer Oasis: Config templates & uncompiled JS/CSS
└── htdocs/              # The Web Face (Entry points index, admin, api)
    ├── _templates/      # Face: Pure UI views (layouts, admin, core)
    ├── libs/            # Brains: Framework logic
    │   ├── src/         # SOUL: Immutable Framework Core (Aether Catalyst)
    │   ├── app/         # HEART: Project-specific logic and models
    │   ├── api/         # NERVES: Closure-based endpoint triggers
    │   └── traits/      # BONES: Structural traits (SQLGetterSetter)
    ├── assets/          # STATIC: Compiled CSS/JS, Images, Fonts
    └── db/              # DATA: Migrations & SQL Schemas
```

---

## 🌟 Modern Project Domains & Modules (v4.0+)
The application layer (`libs/app/` & `libs/api/`) now includes several new modules that have been established during recent development sprints. You must be aware of these components and their intended scopes:

### 1. Channels ("Live Workspace")
- **Heart**: `libs/app/Channel.php`
- **Nerves**: `libs/api/channels/`
- **Focus**: Core channel orchestration. Represents the "Live Workspace" interface, having transitioned from the older "Nodes" branding. Integrates heavily with the UI (e.g., "Create Channel" wizard and "Quick Tip" cards).

### 2. Settings ("Control Center")
- **Heart**: `libs/app/Settings.php`
- **Nerves**: `libs/api/settings/`
- **Focus**: The platform's settings dashboard. Designed as a "Command Orchestration" suite with stylized feature toggles, real-time telemetry elements, and is customized for Light/Dark mode resilience.

### 3. Identity, Roles & Compliance ("Zero-Trust")
- **Heart**: `libs/app/Role.php`, `libs/app/Compliance.php`
- **Nerves**: `libs/api/roles/`, `libs/api/compliance/`
- **Focus**: Enforcing "Zero-Trust Identity Lockdown" and robust security governance. Also dictates structures for volunteer enrolment tracking (Address, Taluka, District, State).

### 4. Messaging & Platform Health
- **Heart**: `libs/app/Message.php`, `libs/app/DashboardStats.php`
- **Nerves**: `libs/api/messages/`, `libs/api/dashboard/`
- **Focus**: Manages messaging states and dashboard analytics. Integrates conceptually with systems like Supabase, Firebase FCM, Tinode, and RabbitMQ to act as the central nervous system for platform telemetry.

---

**CRITICAL COMPLIANCE**: When "learning" this project, your goal is **Zero Variance**. Replicate these tables, patterns, and soul-vs-body separations perfectly in all generated code.
