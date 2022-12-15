import React, {useCallback} from "react"
import "./RMFileTree.scss"
import {Link, useLocation} from "react-router-dom"
import {Directory, File, useRMFileTreeQuery, useSelectFileForSyncMutation} from "../../../store/api/apiRoot"
import toast from "react-hot-toast"
import _ from "lodash"

const iconStyle = {width: "24px", height: "24px", display: "inline-block"}

const DirectoryIcon = () => <svg className="bi" fill="none"
                                 style={iconStyle}
                                 viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
    <path strokeLinecap="round" strokeLinejoin="round"
          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
</svg>

const FileIcon = () => <svg style={iconStyle} fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
    <path strokeLinecap="round" strokeLinejoin="round"
          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
</svg>

function DirectoryItem({item}: { item: Directory }) {
    return <>
        <DirectoryIcon/> <Link to={`/dashboard?path=${item.path}`}>{item.name}</Link>
    </>
}

function FileItem({item}: { item: File }) {
    const [selectForSync, {}] = useSelectFileForSyncMutation()

    const syncFile = useCallback(_.debounce(() => {
        toast.success(`File ${item.name} will be synced!`)
        selectForSync(item.path)
    }, 1000), [item, selectForSync])

    return <><FileIcon/><span onClick={syncFile}>{item.name}</span></>
}

export default function RMFileTree() {
    const {search} = useLocation()
    const params = new URLSearchParams(search)
    const path = params.get("path")
    const {data: filetree, isLoading} = useRMFileTreeQuery(path ?? "/")

    return <div className="container">
        {isLoading ? "loading" : <div id="filetree">
            <h2>{filetree.cwd}</h2>
            <ul>
                {filetree.items.map((item) => {
                    if (item.type === "d") {
                        return <li><DirectoryItem item={item as Directory}/></li>
                    }
                    return <li><FileItem item={item as File}/></li>
                })}
            </ul>
        </div>}
    </div>
}
