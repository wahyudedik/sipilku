# UI Component Library

Koleksi komponen UI yang dapat digunakan di seluruh aplikasi.

## Components

### Card
Komponen card untuk menampilkan konten dalam container yang rapi.

```blade
<x-card>
    <x-slot name="header">
        <h3>Card Title</h3>
    </x-slot>
    
    Card content here
    
    <x-slot name="footer">
        Footer content
    </x-slot>
</x-card>
```

### Button
Tombol dengan berbagai variant dan size.

```blade
<x-button variant="primary" size="md">Click Me</x-button>
<x-button variant="danger" size="sm">Delete</x-button>
<x-button variant="outline" size="lg">Cancel</x-button>
```

**Variants:** primary, secondary, success, danger, warning, outline
**Sizes:** sm, md, lg

### Form Group
Wrapper untuk form input dengan label dan error handling.

```blade
<x-form-group label="Email" name="email" required>
    <x-text-input name="email" />
</x-form-group>
```

### Select Input
Select dropdown dengan styling konsisten.

```blade
<x-select-input 
    name="category" 
    :options="['1' => 'Option 1', '2' => 'Option 2']"
    placeholder="Pilih kategori"
/>
```

### Textarea Input
Textarea dengan styling konsisten.

```blade
<x-textarea-input name="description" rows="5">
    Default content
</x-textarea-input>
```

### Alert
Alert box dengan berbagai tipe.

```blade
<x-alert type="success" dismissible>
    Success message
</x-alert>
```

**Types:** success, error, warning, info

### Badge
Badge untuk menampilkan label atau status.

```blade
<x-badge variant="success" size="md">Active</x-badge>
```

**Variants:** default, primary, success, danger, warning, info
**Sizes:** sm, md, lg

### Sidebar Link
Link untuk sidebar navigation dengan active state.

```blade
<x-sidebar-link href="/dashboard" :active="request()->routeIs('dashboard')">
    <svg>...</svg>
    <span>Dashboard</span>
</x-sidebar-link>
```

## Layouts

### app-with-sidebar
Layout dengan sidebar untuk dashboard/admin pages.

```blade
<x-app-with-sidebar>
    <x-slot name="header">
        <h2>Page Title</h2>
    </x-slot>
    
    Page content
</x-app-with-sidebar>
```

