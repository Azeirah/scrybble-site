import {SyncStatus, useSyncStatusQuery} from "../../store/api/apiRoot"
import * as React from "react"
import "./InspectSync.scss"

function SyncItem({created_at, filename, completed, error}: SyncStatus) {
    return <li
        className={`list-group-item d-flex justify-content-between align-items-center ${error ? "list-group-item-danger" : ""} ${completed ? "list-group-item-success" : "list-group-item-warning"}`}>{filename}
        <span className="badge bg-info rounded-pill">{created_at}</span>
    </li>
}

export default function InspectSync() {
    const {data: syncStatus, isSuccess} = useSyncStatusQuery()

    return <div className="page-centering-container" id="inspect-sync">
        <div className="w-75">
            <h1>Sync status</h1>
            <p>Your most recent syncs are displayed below. </p>
            {isSuccess ?
                <>
                    <ul className="list-group bg-dark">
                        {syncStatus.map((item) => <SyncItem {...item}/>)}
                    </ul>
                    <h3 className="mt-4">Legend</h3>
                    <div className="legend">
                        <div>
                            <div className="bg-success"/>
                            File processing succeeded, is ready for download
                        </div>
                        <div>
                            <div className="bg-warning"/>
                            File processing in progress
                        </div>
                        <div>
                            <div className="bg-danger"/>
                            Processing failed, click sync for more info
                        </div>

                    </div>
                </> :
                <div>loading</div>}
        </div>
    </div>
}
