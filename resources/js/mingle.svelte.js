import { hydrate, unmount } from "svelte";

// Selectors for finding Svelte component containers and their mounted instances
const SVELTE_SELECTOR = "[data-svelte]";
const MOUNTED_SELECTOR = "[data-mounted]";
const COMPONENT_PATH_PREFIX = "resources/js/components/";

/**
 * Manages the lifecycle of Svelte components within a Livewire application.
 * Components are mounted into DOM elements with [data-svelte] attributes,
 * which specify the component name in their dataset.svelte property.
 */
class SvelteManager {
    // Maps component paths to their Svelte component definitions
    #components = new Map();
    // Maps Livewire component IDs to their mounted Svelte component instances
    #mountedInstances = new Map();
    #logger;

    constructor(logger = console) {
        this.#logger = logger;
        this.#initializeComponents();
    }

    async #initializeComponents() {
        // Import all Svelte components in the directory tree
        const modules = import.meta.glob("./**/*.svelte", { eager: true });
        for (const [path, module] of Object.entries(modules)) {
            const componentPath = "resources/js" + path.replace("./", "/");
            this.#components.set(componentPath, module.default);
        }
    }

    #createProps(wire, dataset) {
        return new Props(wire, dataset);
    }

    // Finds elements marked for Svelte components that haven't been mounted yet
    #findUnmountedRoots(wire) {
        return wire.$el.querySelectorAll(
            `${SVELTE_SELECTOR}:not(:has(${MOUNTED_SELECTOR}))`,
        );
    }

    // Creates a wrapper div that marks where a Svelte component is mounted
    // This helps track component instances and prevent duplicate mounting
    #createMountPoint(mountId) {
        const child = document.createElement("div");
        child.style.display = "contents";
        child.setAttribute("data-mounted", mountId);
        return child;
    }

    mountComponents(wire) {
        try {
            const roots = this.#findUnmountedRoots(wire);
            for (const root of roots) {
                this.#mountComponent(wire, root);
            }
        } catch (error) {
            this.#logger.error("Error mounting components:", error);
        }
    }

    #mountComponent(wire, root) {
        const componentName = root.dataset.svelte;
        const svelteComponent = this.#components.get(
            COMPONENT_PATH_PREFIX + componentName,
        );

        if (!svelteComponent) {
            this.#logger.warn("No svelte component found for", componentName);
            return;
        }

        const mountId = crypto.randomUUID();
        const props = this.#createProps(wire, root.dataset);
        const mountPoint = this.#createMountPoint(mountId);

        // Replace the original content with our mounting point
        root.replaceChildren(mountPoint);

        // Hydrate creates a Svelte component in place of existing HTML
        // This is used instead of regular mounting to preserve SSR content
        const app = hydrate(svelteComponent, {
            target: mountPoint,
            props,
        });

        this.#storeMountedComponent(wire.id, { mountId, app, props });
    }

    #storeMountedComponent(wireId, componentRef) {
        if (!this.#mountedInstances.has(wireId)) {
            this.#mountedInstances.set(wireId, new Set());
        }
        this.#mountedInstances.get(wireId).add(componentRef);
    }

    updateComponent(wire, root) {
        const child = root.firstChild;
        if (!child?.hasAttribute("data-mounted")) return;

        const mountId = child.getAttribute("data-mounted");
        const componentRef = this.#findMountedComponent(wire.id, mountId);

        if (componentRef) {
            componentRef.props.updateDataset(root.dataset);
        }
    }

    unmountComponent(wireId, root) {
        const child = root.firstChild;
        if (!child?.hasAttribute("data-mounted")) return;

        const mountId = child.getAttribute("data-mounted");
        const componentRef = this.#findMountedComponent(wireId, mountId);

        if (componentRef) {
            this.#unmountComponentRef(wireId, componentRef);
        }
    }

    #unmountComponentRef(wireId, componentRef) {
        try {
            unmount(componentRef.app);

            const mountPoint = document.querySelector(
                `[data-mounted="${componentRef.mountId}"]`,
            );
            if (mountPoint) {
                mountPoint.removeAttribute("data-mounted");
            }

            const instances = this.#mountedInstances.get(wireId);
            instances.delete(componentRef);

            if (instances.size === 0) {
                this.#mountedInstances.delete(wireId);
            }
        } catch (error) {
            this.#logger.error("Error unmounting component:", error);
        }
    }

    #findMountedComponent(wireId, mountId) {
        const instances = this.#mountedInstances.get(wireId);
        return instances
            ? Array.from(instances).find((ref) => ref.mountId === mountId)
            : null;
    }

    updateSnapshots(wireId, snapshot) {
        const instances = this.#mountedInstances.get(wireId);
        if (!instances) return;

        for (const ref of instances) {
            ref.props.updateSnapshot(extractData(JSON.parse(snapshot)));
        }
    }

    cleanup(wireId) {
        const instances = this.#mountedInstances.get(wireId);
        if (!instances) return;

        for (const ref of instances) {
            this.#unmountComponentRef(wireId, ref);
        }
    }
}

/**
 * The data that's passed between the browser and server is in the form of
 * nested tuples consisting of the schema: [rawValue, metadata]. In this
 * method we're extracting the plain JS object of only the raw values.
 */
export function extractData(payload) {
    let value = isSynthetic(payload) ? payload[0] : payload;
    let meta = isSynthetic(payload) ? payload[1] : undefined;

    if (isObjecty(value)) {
        Object.entries(value).forEach(([key, iValue]) => {
            value[key] = extractData(iValue);
        });
    }

    return value;
}

export function isObjecty(subject) {
    return typeof subject === "object" && subject !== null;
}

/**
 * Determine if the variable passed in is a node in a nested metadata
 * tuple tree. (Meaning it takes the form of: [rawData, metadata])
 */
export function isSynthetic(subject) {
    return (
        Array.isArray(subject) &&
        subject.length === 2 &&
        typeof subject[1] === "object" &&
        Object.keys(subject[1]).includes("s")
    );
}
/**
 * Props acts as a bridge between Livewire and Svelte components.
 * - wire: Reference to the Livewire component instance
 * - data: Reactive state that updates when Livewire data changes
 * - dataset: Reactive state that updates when the container's dataset changes
 */
class Props {
    wire = null;
    // $state makes these properties reactive in Svelte components
    snapshot = $state({});
    dataset = $state({});

    constructor(wire, dataset) {
        this.wire = wire;
        this.updateDataset(dataset);
        this.updateSnapshot(extractData(wire.__instance.snapshot));
    }

    updateSnapshot(snapshot) {
        this.snapshot = snapshot.data;
    }

    updateDataset(dataset) {
        // spread to convert DOM dataset to object as dataset is not a plain object
        this.dataset = Object.fromEntries(
            Object.entries(dataset).map(([key, val]) => {
                try {
                    return [key, JSON.parse(val)];
                } catch (e) {
                    return [key, val];
                }
            }),
        );
    }
}

const svelteManager = new SvelteManager();

/**
 * Polyfills for requestIdleCallback and cancelIdleCallback for Safari
 * https://github.com/pladaria/requestidlecallback-polyfill/blob/master/index.js
 */
window.requestIdleCallback =
    window.requestIdleCallback ||
    function (cb) {
        var start = Date.now();
        return setTimeout(function () {
            cb({
                didTimeout: false,
                timeRemaining: function () {
                    return Math.max(0, 50 - (Date.now() - start));
                },
            });
        }, 1);
    };

window.cancelIdleCallback =
    window.cancelIdleCallback ||
    function (id) {
        clearTimeout(id);
    };

// Hook Timing Guide:
// 1. component.init - Called when Livewire component is initialized
// 2. effect - Called after Livewire updates the DOM
// 3. morph.updated - Called for each element that was updated
// 4. morph.removing - Called before an element is removed
// 5. morph.removed - Called after an element is removed
// 6. commit - Called when all updates are complete

// Wait for DOM updates before mounting to ensure the container elements exist
Livewire.hook("effect", ({ component }) => {
    requestIdleCallback(
        () => {
            svelteManager.mountComponents(component.$wire);
        },
        { timeout: 1 },
    );
});

// Update Svelte component data when Livewire state changes
Livewire.hook("commit", ({ component, succeed }) => {
    succeed(({ snapshot }) => {
        svelteManager.updateSnapshots(component.id, snapshot);
    });
});

// Clean up Svelte components when Livewire component is destroyed
Livewire.hook("component.init", ({ component, cleanup }) => {
    cleanup(() => svelteManager.cleanup(component.id));
});

// Update Svelte component props when its container is modified
Livewire.hook("morph.updated", ({ el, component }) => {
    if (el.hasAttribute("data-svelte")) {
        svelteManager.updateComponent(component.$wire, el);
    }
});

// Prevent Livewire from removing mounted component containers
// wire.ignore doesn't prevent child removal, so we manually skip
Livewire.hook("morph.removing", ({ el, skip }) => {
    if (el.hasAttribute("data-mounted")) {
        skip();
    }
});

// Clean up Svelte components when their containers are removed
Livewire.hook("morph.removed", ({ el, component }) => {
    const roots = el.querySelectorAll(
        `${SVELTE_SELECTOR}:has(${MOUNTED_SELECTOR})`,
    );
    if (roots.length) {
        roots.forEach((root) =>
            svelteManager.unmountComponent(component.id, root),
        );
    } else if (el.hasAttribute("data-svelte")) {
        svelteManager.unmountComponent(component.id, el);
    }
});
