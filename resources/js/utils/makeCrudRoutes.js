import { capital, kebab, pascal } from "case"
import pluralize                  from "pluralize"

export default function makeCrudRoutes(
    {
        name,
        path,
        isChildComponent,
        modelId,
        modelIdRegex,
        components: {
            index,
            list,
            view,
            edit,
            create,
        },
    },
) {
    const indexRoute = {
        path: "",
        component: "component" in list ? list.component : list,
        name: pascal(pluralize.plural(name)),
        props: (route) => ({
            ids: route.params.ids,
            search: route.query.search,
            page: Number(route.query.page) || 1,
            perPage: Number(route.query.per_page) || 12,
            sort: route.query.sort,
            ...("props" in list ? list.props(route) : []),
        }),
        meta: {
            requiresAuth: true,
            ...("meta" in list ? list.meta : []),
        },
    }

    const model_id = modelId || "modelId"
    const model_id_regex = modelIdRegex || "\\d+"
    const viewComponent = view || edit

    const viewRoute = {
        path: "path" in viewComponent ? viewComponent.path : `:${model_id}(${model_id_regex})/`,
        component: "component" in viewComponent ? viewComponent.component : viewComponent,
        props: (route) => ({
            modelId: route.params[modelId || "modelId"],
            locked: true,
            ...("props" in viewComponent ? viewComponent.props(route) : []),
        }),
        name: `View${pascal(pluralize.singular(name))}`,
        meta: {
            requiresAuth: true,
            breadcrumb: "View",
            ...("meta" in viewComponent ? viewComponent.meta : []),
        },
        children: [
            ...("children" in viewComponent ? viewComponent.children : []),
        ],
    }

    const editRoute = {
        path: "path" in edit ? edit.path : `:${model_id}(${model_id_regex})/edit/`,
        component: "component" in edit ? edit.component : edit,
        props: (route) => ({
            modelId: route.params[modelId || "modelId"],
            ...("props" in edit ? edit.props(route) : []),
        }),
        name: `Edit${pascal(pluralize.singular(name))}`,
        meta: {
            requiresAuth: true,
            breadcrumb: "Edit",
            ...("meta" in edit ? edit.meta : []),
        },
    }

    const createRoute = {
        path: "path" in create ? create.path : "new/",
        component: "component" in create ? create.component : create,
        props: (route) => ({
            ...("props" in create ? create.props(route) : []),
        }),
        name: `Create${pascal(pluralize.singular(name))}`,
        meta: {
            requiresAuth: true,
            breadcrumb: "Create",
            nestedCreation: Boolean(isChildComponent),
            ...("meta" in create ? create.meta : []),
        },
    }

    return {
        path: path || (isChildComponent ? `${kebab(pluralize.plural(name))}/` : `/${kebab(pluralize.plural(name))}/`),
        component: "component" in index ? index.component : index,
        meta: {
            requiresAuth: true,
            breadcrumb: capital(name),
        },
        children: [
            indexRoute,
            viewRoute,
            editRoute,
            createRoute,
        ],
    }
}
