import React, {useCallback} from "react"
import "./RMFileTree.scss"
import {Link, useLocation, useNavigate} from "react-router-dom"
import {Directory, File, useRMFileTreeQuery, useSelectFileForSyncMutation} from "../../../store/api/apiRoot"
import toast from "react-hot-toast"
import _ from "lodash"
import {faFile, faFolder, faHighlighter} from "@fortawesome/free-solid-svg-icons"
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome"

const syncSettingsEnabled = false;

const DirectoryIcon = () => <FontAwesomeIcon icon={faFolder}/>

const FileIcon = () => <FontAwesomeIcon icon={faFile}/>

function DirectoryItem({item}: { item: Directory }) {
    return <span>
        <Link to={`/dashboard?path=${item.path}`}>{item.name}</Link>
    </span>
}

function FileItem({item}: { item: File }) {
    return <span>{item.name}</span>
}

function SyncSettings() {
    return <div role="group" aria-label="Synchronization settings" className="btn-group btn-group-sm">
        <button className="btn btn-primary"><FontAwesomeIcon icon={faHighlighter}/></button>
    </div>
}

export default function RMFileTree() {
    const {search} = useLocation()
    const params = new URLSearchParams(search)
    const path = params.get("path")
    const {data: filetree, isLoading} = useRMFileTreeQuery(path ?? "/")
    const navigate = useNavigate()

    const [selectForSync, {}] = useSelectFileForSyncMutation()

    const syncFile = useCallback(_.debounce((item: File) => {
        toast.success(`File ${item.name} will be synced!`)
        selectForSync(item.path)
    }, 1000), [selectForSync])

    return <div className="container">
        {isLoading ? "loading" : <div id="filetree">
            <table className="table table-dark table-sm table-hover table-striped align-middle caption-top">
                <caption>{filetree.cwd}</caption>
                <colgroup>
                    <col width="0*"/>
                    <col/>
                    <col width="0*"/>
                </colgroup>
                <thead>
                <tr>
                    <th></th>
                    <th>File or directory</th>
                    {syncSettingsEnabled ? <th>Sync settings</th> : <></>}
                </tr>
                </thead>
                <tbody>
                {filetree.items.map((item) => {
                    if (item.type === "d") {
                        return <tr className="directory" role="link"
                                   onClick={() => navigate(`/dashboard?path=${item.path}`)}>
                            <td></td>
                            <td><DirectoryItem item={item as Directory}/></td>
                            <td></td>
                        </tr>
                    }
                    return <tr>
                        <td style={{width: "fit-content", padding: "8px"}}>
                            <button className="btn btn-success btn-sm" onClick={() => {
                                syncFile(item as File)
                            }}>Sync now
                            </button>
                        </td>
                        <td className="file"><FileItem item={item as File}/></td>
                        {syncSettingsEnabled ? <td style={{textAlign: "right", padding: "0 8px"}}><SyncSettings/></td> : <></>}
                    </tr>
                })}
                </tbody>
            </table>
        </div>}
    </div>
}
