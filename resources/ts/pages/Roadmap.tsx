import * as React from "react"

export function Roadmap() {
    return <div className="container">
        <h1>Scrybble roadmap</h1>
        <table className="table table-dark">
            <thead>
            <tr>
                <th>Supported</th>
                <th>Feature</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr className="table-group-divider">
                <td colSpan={3} className="table-dark text-center">
                    <h4 style={{marginBottom: 0}}>Filetype support</h4>
                </td>
            </tr>
            <tr className="table-success">
                <td>Yes</td>
                <td>Support PDFs</td>
                <td>Allow synchronization of PDF files from ReMarkable to Obsidian</td>
            </tr>
            <tr className="table-success">
                <td>Yes</td>
                <td>Support .epub format</td>
                <td>Allow synchronization of .epub files from ReMarkable to Obsidian</td>
            </tr>
            <tr className="table-success">
                <td>Yes</td>
                <td>Support notebooks</td>
                <td>Allows synchronizing ReMarkable notebooks to Obsidian</td>
            </tr>
            <tr className="table-success">
                <td>Yes</td>
                <td>Quick sheets</td>
                <td>Allow ssynchronizing quick sheets to Obsidian</td>
            </tr>
            <tr className="table-group-divider">
                <td colSpan={3} className="table-dark text-center">
                    <h4 style={{marginBottom: 0}}>Features</h4>
                </td>
            </tr>
            <tr className="table-warning">
                <td>No</td>
                <td>Tags</td>
                <td>ReMarkable recently released a tags feature, it would be great if the synchronized pages contain
                    these references.
                </td>
            </tr>
            <tr className="table-group-divider">
                <td colSpan={3} className="table-dark text-center">
                    <h4 style={{marginBottom: 0}}>Website user interface</h4>
                </td>
            </tr>
            <tr className="table-success">
                <td>Yes (beta)</td>
                <td>Show synchronization status</td>
                <td>There are multiple steps to synchronize a file from RM to Obsidian, since mid-December 2022, we have
                    a
                    "Sync status" page to inspect what does and doesn't sync as expected.
                </td>
            </tr>
            <tr className="table-group-divider">
                <td colSpan={3} className="table-dark text-center">
                    <h4 style={{marginBottom: 0}}>Stability</h4>
                </td>
            </tr>
            <tr className="table-info">
                <td>-</td>
                <td>Stability</td>
                <td>Scrybble has become more stable since it's initial release. Over 50 customers depend on scrybble!
                </td>
            </tr>
            <tr className="table-group-divider">
                <td colSpan={3} className="table-dark text-center">
                    <h4 style={{marginBottom: 0}}>Synchronization</h4>
                </td>
            </tr>
            <tr className="table-warning">
                <td>No</td>
                <td>Automatically sync on file change</td>
                <td>At the moment, you'll have to log in to the web interface and click a file to start the
                    synchronisation process. It would be preferable if this happened automatically, this will be added
                    at a later time.
                </td>
            </tr>
            <tr className="table-warning">
                <td>No</td>
                <td>Only sync pages with notes</td>
                <td>An e-book can have 400 pages, where only 17 of them contain annotations. With this option, only
                    those 17 pages will show up in your vault.
                </td>
            </tr>
            <tr className="table-warning">
                <td>Yes</td>
                <td>Export highlights to markdown</td>
                <td>Obsidian is best for working with text, with this option you will be able to choose if you want to
                    export your highlights to a plain-text markdown file alongside the pdf.
                </td>
            </tr>
            </tbody>
        </table>
        <h2>Contact us</h2>
        <p>Got feedback? Something missing?</p>
        <span>smg@smgmusicdisplay.com</span>
    </div>
}
