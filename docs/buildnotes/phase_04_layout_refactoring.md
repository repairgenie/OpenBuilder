# Phase 4: Layout Refactoring

## Goal
Break the HTML into reusable layout and partial files.

## Tasks
1. [ ] Extract `<head>` and header into `layouts/header.php`.
2. [ ] Extract sidebar into `layouts/sidebar.php`.
3. [ ] Extract footer into `layouts/footer.php`.
4. [ ] Implement a basic `render()` function to combine these.

## Rationale
Reduces code duplication and makes it easier to update global UI elements.