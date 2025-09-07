# Available Variables

The following methods are available to call in your Twig templates:

### `craft.menuManager.nodes(params)`
The `params` parameter can be either a string for the [Nav](docs:developers/nav) handle, or an object of [NodeQuery](docs:getting-elements/node-queries) params. You can also chain these same params to this function call.

```twig
{# Fetch the `mainMenu` nodes #}
{% set nodes = craft.menuManager.nodes('mainMenu').all() %}

{# Chain params to the `nodes()` function #}
{% set nodes = craft.menuManager.nodes()
    .handle('mainMenu')
    .site('default')
    .all() %}

{# Or, pass them as an object #}
{% set nodes = craft.menuManager.nodes({
    handle: 'mainMenu',
    site: 'default',
}).all() %}
```

See [Node Queries](docs:getting-elements/node-queries)

### `craft.menuManager.render(params, options)`
The `params` parameter can be either a string for the [Nav](docs:developers/nav) handle, an object of [NodeQuery](docs:getting-elements/node-queries) params or a [NodeQuery](docs:getting-elements/node-queries) itself.

```twig
{# Render the `mainMenu` navigation #}
{{ craft.menuManager.render('mainMenu') }}

{# Render the `mainMenu` navigation for the `default` site #}
{{ craft.menuManager.render({
    handle: 'mainMenu',
    site: 'default',
}) }}

{# The same as above, but using a `NodeQuery` #}
{% set nodeQuery = craft.menuManager.nodes('mainMenu').site('default') %}

{{ craft.menuManager.render(nodeQuery) }}
```

See [Rendering Nodes](docs:template-guides/rendering-nodes)

### `craft.menuManager.breadcrumbs(options)`
See [Breadcrumbs](docs:template-guides/breadcrumbs)

### `craft.menuManager.getActiveNode(params, includeChildren)`
The `params` parameter can be either a string for the [Nav](docs:developers/nav) handle, an object of [NodeQuery](docs:getting-elements/node-queries) params or a [NodeQuery](docs:getting-elements/node-queries) itself.

See [Rendering Nodes](docs:template-guides/rendering-nodes)

### `craft.menuManager.tree(params)`
Returns a full tree structure of nodes as a nested array.

The `params` parameter can be either a string for the [Nav](docs:developers/nav) handle, an object of [NodeQuery](docs:getting-elements/node-queries) params or a [NodeQuery](docs:getting-elements/node-queries) itself.

### `craft.menuManager.getNavById(id)`
Returns the navigation for the provided id.

### `craft.menuManager.getNavByHandle(handle)`
Returns the navigation for the provided handle.
