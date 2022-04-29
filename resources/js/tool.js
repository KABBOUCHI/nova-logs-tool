import LogsTool from "./components/LogsTool";

Nova.booting((app, store) => {
    Nova.inertia('Logs', LogsTool)
})
