export interface RMTreeItem {
    type: "f" | "d"
    name: string,
    path: string
}

export interface File extends RMTreeItem {
    type: "f",
}

export interface Directory extends RMTreeItem {
    type: "d",
}
