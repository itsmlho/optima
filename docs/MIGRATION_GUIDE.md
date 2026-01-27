# CodeIgniter 4 Migration Strategy

## Current State
The database table structure currently exists in Production (`optima_ci` DB) but might not be fully reflected in the migration files found in `app/Database/Migrations`.

## Aligning Migrations
Since the application is already live/deployed, **do not** run `php spark migrate:refresh` as it will drop all data.

### How to Check Status
Run the following in the terminal:
```bash
php spark migrate:status
```
This shows which migration files have been "run" recorded in the `migrations` table.

### Creating New Migrations
When adding new features (e.g., adding a column to `spk`):

1.  **Generate Migration File**:
    ```bash
    php spark make:migration AddColumnXToSpk
    ```
2.  **Edit the File** (`app/Database/Migrations/YYYY-MM-DD-timestamp_AddColumnXToSpk.php`):
    ```php
    public function up()
    {
        $this->forge->addColumn('spk', [
            'new_column' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('spk', 'new_column');
    }
    ```
3.  **Run the Migration**:
    ```bash
    php spark migrate
    ```

## Reverse Engineering (If Migrations Missing)
If tables exist in SQL but NOT in `app/Database/Migrations`, you treat the current Database as "Version 0". 
Any **new** changes must be done via `php spark migrate` to ensure future deployment consistency.

**Do not manually edit database columns via PHPMyAdmin** going forward. Always use Migrations.
