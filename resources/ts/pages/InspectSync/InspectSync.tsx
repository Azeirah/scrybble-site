import {SyncStatus, useSyncStatusQuery} from "../../store/api/apiRoot.js"
import * as React from "react"
import {useEffect, useRef, useState} from "react"
import "./InspectSync.scss"
import {Dialog} from "../../components/reusable/Dialog/Dialog.js";
import {faQuestionCircle} from "@fortawesome/free-solid-svg-icons"
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome"

type FileSyncHandle = {
    id: number, variant: "error" | "success", filename: SyncStatus["filename"]
};

function SyncItem({id, created_at, filename, completed, error, openDialog}: SyncStatus & {
    openDialog: React.Dispatch<FileSyncHandle | false>
}) {
    return <li
        className={`list-group-item gap-2 ${error ? "list-group-item-danger" : ""} ${completed ? "list-group-item-success" : "list-group-item-warning"}`}>
        <span className="filename">{filename}</span>
        <span className="badge bg-info rounded-pill">{created_at}</span>
        <a onClick={() => {
            openDialog({
                id, filename, variant: completed ? "success" : "error"
            });
        }} className="share-error">Share info with developer</a>
    </li>
}

function ShareDialog({shareInfoDialogOpen, setShareInfoDialogOpen}: {
    shareInfoDialogOpen: false | FileSyncHandle, setShareInfoDialogOpen: React.Dispatch<FileSyncHandle | false>
}) {
    const [devAccess, setDevAccess] = useState(false);
    const [openAccess, setOpenAccess] = useState(false);

    const formRef = useRef<HTMLFormElement>();

    const isSuccess = shareInfoDialogOpen?.['variant'] === "success";
    const isError = shareInfoDialogOpen?.['variant'] === "error";

    return <Dialog className="share-info-dialog" open={shareInfoDialogOpen !== false}
                   close={() => setShareInfoDialogOpen(false)}
                   title={"Your ReMarkable document isn't syncing well. Need help?"}
                   actions={<div className="d-flex gap-2">
                       <button className="btn btn-warning" disabled={!devAccess && !openAccess} onClick={() => {

                       }}>Share this file
                       </button>
                       <button onClick={() => setShareInfoDialogOpen(false)} className="btn btn-primary">
                           I don't want to share this file at all
                       </button>
                   </div>}>
        <>
            <p>
                Your privacy matters. The Scrybble developers cannot just access your files.
            </p>
            <p>
                Would you like to give permission to share the ReMarkable document files that Scrybble downloaded so
                that it can be investigated for {shareInfoDialogOpen['variant'] === "success" ? "problems" : "errors"}?
            </p>

            <form className="form-group" ref={formRef}>
                <div className="form-check">
                    <input className="form-check-input" id="check-developer-access" type="checkbox" checked={devAccess}
                           onChange={() => {
                               if (devAccess) {
                                   setDevAccess(false);
                                   setOpenAccess(false);
                               } else {
                                   setDevAccess(true);
                               }
                           }}/>
                    <label className="form-check-label" htmlFor="check-developer-access">Allow the
                        developer to access this ReMarkable file</label>
                </div>
                <div className={`collapse${devAccess ? " show" : ""} given-access`}>
                    <div className="form-check" id="open-access-collapse">
                        <input className="form-check-input" id="check-open-access" type="checkbox"
                               checked={openAccess}
                               onChange={() => {
                                   setOpenAccess((open) => !open);
                               }}/>
                        <label className="form-check-label" htmlFor="check-open-access">Share this file with
                            the wider ReMarkable development community (This means anyone can access it)</label>
                    </div>
                    <a className="btn btn-info btn-sm" href="#more-info-collapse"
                       data-bs-toggle="collapse"
                       data-bs-target="#more-info-collapse"
                       role="button"
                       aria-controls="more-info-collapse">
                        <FontAwesomeIcon icon={faQuestionCircle}/>{" "}Why share with the broader community?
                    </a>
                    <div className="collapse mx-4" id="more-info-collapse">
                        <p>
                            Sharing your file with everyone may feel strange. Why would you want that?
                            It's because Scrybble and the ReMarkable development community is built on <a
                            href="/open-core">open
                            principles</a>.
                        </p>
                        <p>When you share a file with the wider community, it means <a href="/contributors">any
                            developer</a> is
                            allowed to use it to make
                            sure their application works well for you. Not just Scrybble.
                        </p>
                        <label htmlFor="comment">What's wrong with this ReMarkable document?</label>
                        <p className="text-success">
                            Want a thriving community of
                            tools for the ReMarkable? You can contribute your files to the community!</p>
                    </div>
                    <div className="mt-4">
                        <label htmlFor="comment">What's up with this document?</label>
                        <textarea className="form-control" placeholder="I expected [...] but [...] happened instead :("
                                  name="comment" id="comment"/>
                    </div>
                </div>
            </form>

            <hr/>

            <div className="mt-2">
                <h5>What will be shared?</h5>
                <div>
                    {devAccess ? <ul>
                        {isError ?
                            <li>The errors associated with this ReMarkable document, pdf or quick sheets.</li> : null}
                        <li>Your ReMarkable document: <pre>{shareInfoDialogOpen['filename']}</pre></li>
                    </ul> : "Nothing. You haven't given permission to share."}
                </div>
            </div>

            <div className="mt-2">
                <h5>Who will it be shared with?</h5>
                <div>
                    {devAccess && openAccess ? "This file will be shared with anyone." : null}
                    {devAccess && !openAccess ? "This file will be shared with the developer(s) of Scrybble" : null}
                    {!devAccess && !openAccess ? "Nobody. You haven't given permission to share." : null}
                </div>
            </div>
        </>
    </Dialog>
}

export default function InspectSync() {
    const [shouldPoll, setShouldPoll] = useState(false);
    const [shareInfoDialogOpen, setShareInfoDialogOpen] = useState<false | FileSyncHandle>(false);
    const {data: syncStatus, isSuccess} = useSyncStatusQuery(undefined, {
        refetchOnMountOrArgChange: true, pollingInterval: shouldPoll ? 1000 : 0
    })

    useEffect(() => {
        if (isSuccess && syncStatus && syncStatus.find((status) => !status.completed && !status.error)) {
            setShouldPoll(true)
        } else {
            setShouldPoll(false)
        }
    }, [])

    return <div className="page-centering-container" id="inspect-sync">
        <ShareDialog shareInfoDialogOpen={shareInfoDialogOpen} setShareInfoDialogOpen={setShareInfoDialogOpen}/>
        <div className="w-75">
            <h1>Sync status</h1>
            <p>Your most recent syncs are displayed below. </p>
            {isSuccess ? <>
                <ul className="list-group bg-dark syncs">
                    {syncStatus.map((item) => <SyncItem key={item.id} {...item}
                                                        openDialog={setShareInfoDialogOpen}/>)}
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
                        Processing failed, you can share info with the developer
                    </div>

                </div>
            </> : <div>loading</div>}
        </div>
    </div>
}
