# Graph Report - .  (2026-06-22)

## Corpus Check
- Corpus is ~31,393 words - fits in a single context window. You may not need a graph.

## Summary
- 209 nodes · 154 edges · 72 communities (52 shown, 20 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 12 edges (avg confidence: 0.9)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Composer Autoloading|Composer Autoloading]]
- [[_COMMUNITY_Laravel Engineering Standards|Laravel Engineering Standards]]
- [[_COMMUNITY_Frontend Build Tooling|Frontend Build Tooling]]
- [[_COMMUNITY_User Authentication Model|User Authentication Model]]
- [[_COMMUNITY_Default Feature Test|Default Feature Test]]
- [[_COMMUNITY_Development Dependencies|Development Dependencies]]
- [[_COMMUNITY_Composer Automation Scripts|Composer Automation Scripts]]
- [[_COMMUNITY_Composer Plugin Settings|Composer Plugin Settings]]
- [[_COMMUNITY_OpenCode Configuration|OpenCode Configuration]]
- [[_COMMUNITY_User Factory|User Factory]]
- [[_COMMUNITY_Application Service Provider|Application Service Provider]]
- [[_COMMUNITY_Security Testing Validation|Security Testing Validation]]
- [[_COMMUNITY_POS Digital Menu|POS Digital Menu]]
- [[_COMMUNITY_Data Access Practices|Data Access Practices]]
- [[_COMMUNITY_Laravel Boost Integration|Laravel Boost Integration]]
- [[_COMMUNITY_Order Payment Receipt|Order Payment Receipt]]
- [[_COMMUNITY_Asynchronous Notifications|Asynchronous Notifications]]
- [[_COMMUNITY_Base HTTP Controller|Base HTTP Controller]]
- [[_COMMUNITY_Laravel Filament Stack|Laravel Filament Stack]]
- [[_COMMUNITY_Laravel Framework Overview|Laravel Framework Overview]]
- [[_COMMUNITY_Architecture Caching Practices|Architecture Caching Practices]]
- [[_COMMUNITY_Basic Inventory|Basic Inventory]]
- [[_COMMUNITY_Daily Sales Reporting|Daily Sales Reporting]]
- [[_COMMUNITY_MySQL Database|MySQL Database]]
- [[_COMMUNITY_Order History|Order History]]
- [[_COMMUNITY_Staff Login|Staff Login]]
- [[_COMMUNITY_Crawler Policy|Crawler Policy]]
- [[_COMMUNITY_Configuration Practices|Configuration Practices]]
- [[_COMMUNITY_Eloquent Practices|Eloquent Practices]]
- [[_COMMUNITY_Error Handling Practices|Error Handling Practices]]
- [[_COMMUNITY_HTTP Client Practices|HTTP Client Practices]]
- [[_COMMUNITY_Migration Practices|Migration Practices]]
- [[_COMMUNITY_Routing Practices|Routing Practices]]
- [[_COMMUNITY_Scheduling Practices|Scheduling Practices]]
- [[_COMMUNITY_Code Style Practices|Code Style Practices]]

## God Nodes (most connected - your core abstractions)
1. `Laravel Best Practices Framework` - 21 edges
2. `require-dev` - 9 edges
3. `scripts` - 9 edges
4. `User` - 6 edges
5. `TestCase` - 6 edges
6. `config` - 5 edges
7. `AppServiceProvider` - 4 edges
8. `require` - 4 edges
9. `psr-4` - 4 edges
10. `UserFactory` - 4 edges

## Surprising Connections (you probably didn't know these)
- `Laravel Best Practices Framework Mirror` --semantically_similar_to--> `Laravel Best Practices Framework`  [INFERRED] [semantically similar]
  .claude/skills/laravel-best-practices/SKILL.md → .agents/skills/laravel-best-practices/SKILL.md
- `Laravel Boost Guidelines` --conceptually_related_to--> `Validation & Forms Best Practices`  [INFERRED]
  AGENTS.md → .claude/skills/laravel-best-practices/rules/validation.md
- `Laravel Boost Guidelines` --semantically_similar_to--> `Laravel Boost Guidelines`  [EXTRACTED] [semantically similar]
  AGENTS.md → CLAUDE.md
- `Advanced Query Patterns` --semantically_similar_to--> `Database Performance Best Practices`  [INFERRED] [semantically similar]
  .agents/skills/laravel-best-practices/rules/advanced-queries.md → .agents/skills/laravel-best-practices/rules/db-performance.md
- `Laravel Boost Guidelines` --references--> `Testing Best Practices`  [EXTRACTED]
  AGENTS.md → .claude/skills/laravel-best-practices/rules/testing.md

## Import Cycles
- None detected.

## Communities (72 total, 20 thin omitted)

### Community 0 - "Composer Autoloading"
Cohesion: 0.08
Nodes (23): autoload, autoload-dev, psr-4, psr-4, description, extra, laravel, keywords (+15 more)

### Community 1 - "Laravel Engineering Standards"
Cohesion: 0.11
Nodes (22): Laravel Best Practices Framework Mirror, Laravel Best Practices Framework, Advanced Query Patterns, Architecture Best Practices, Blade and Views Best Practices, Caching Best Practices, Collections Best Practices, Configuration Best Practices (+14 more)

### Community 2 - "Frontend Build Tooling"
Cohesion: 0.15
Nodes (12): devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private, $schema (+4 more)

### Community 3 - "User Authentication Model"
Cohesion: 0.25
Nodes (7): Authenticatable, HasFactory, User, Notifiable, Seeder, DatabaseSeeder, WithoutModelEvents

### Community 4 - "Default Feature Test"
Cohesion: 0.28
Nodes (4): BaseTestCase, ExampleTest, TestCase, ExampleTest

### Community 5 - "Development Dependencies"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/boost, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 6 - "Composer Automation Scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 7 - "Composer Plugin Settings"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 8 - "OpenCode Configuration"
Cohesion: 0.29
Nodes (6): command, enabled, type, mcp, laravel-boost, $schema

### Community 9 - "User Factory"
Cohesion: 0.47
Nodes (3): UserFactory, Factory, static

### Community 11 - "Security Testing Validation"
Cohesion: 0.40
Nodes (5): Security Best Practices, Testing Best Practices, Validation & Forms Best Practices, Laravel Boost Guidelines, Laravel Boost Guidelines

### Community 12 - "POS Digital Menu"
Cohesion: 0.50
Nodes (4): Digital Menu, Menu Management, QR-code Menu, Small Coffee Shop POS System

### Community 13 - "Data Access Practices"
Cohesion: 0.50
Nodes (4): Advanced Query Patterns, Blade & Views Best Practices, Collection Best Practices, Database Performance Best Practices

### Community 18 - "Order Payment Receipt"
Cohesion: 0.67
Nodes (3): Order Management, Payment Calculation, Receipt Printing

### Community 19 - "Asynchronous Notifications"
Cohesion: 0.67
Nodes (3): Events & Notifications Best Practices, Mail Best Practices, Queue Jobs Best Practices

## Knowledge Gaps
- **96 isolated node(s):** `php`, `Controller`, `$schema`, `name`, `type` (+91 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **20 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `require-dev` connect `Development Dependencies` to `Composer Autoloading`?**
  _High betweenness centrality (0.016) - this node is a cross-community bridge._
- **Why does `scripts` connect `Composer Automation Scripts` to `Composer Autoloading`?**
  _High betweenness centrality (0.016) - this node is a cross-community bridge._
- **Why does `config` connect `Composer Plugin Settings` to `Composer Autoloading`?**
  _High betweenness centrality (0.012) - this node is a cross-community bridge._
- **What connects `php`, `Controller`, `$schema` to the rest of the system?**
  _97 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Composer Autoloading` be split into smaller, more focused modules?**
  _Cohesion score 0.08333333333333333 - nodes in this community are weakly interconnected._
- **Should `Laravel Engineering Standards` be split into smaller, more focused modules?**
  _Cohesion score 0.11255411255411256 - nodes in this community are weakly interconnected._