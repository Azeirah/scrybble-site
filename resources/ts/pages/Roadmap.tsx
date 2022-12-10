import * as React from "react";

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
            <tr className="table-warning">
                <td>No</td>
                <td>Support .epub format</td>
                <td>Idem, see above</td>
            </tr>
            <tr className="table-warning">
                <td>No</td>
                <td>Support .mobi format</td>
                <td>Idem, see above</td>
            </tr>
            <tr className="table-danger">
                <td>No</td>
                <td>Quick sheets and notebooks</td>
                <td>Unlikely to be added soon.</td>
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
                    these references somehow.
                </td>
            </tr>
            <tr className="table-group-divider">
                <td colSpan={3} className="table-dark text-center">
                    <h4 style={{marginBottom: 0}}>Website user interface</h4>
                </td>
            </tr>
            <tr className="table-warning">
                <td>No</td>
                <td>Show synchronization status</td>
                <td>There are multiple steps to synchronize a file from RM to Obsidian, there is currently no
                    feedback on how this process is proceeding .
                </td>
            </tr>
            <tr className="table-warning">
                <td>No</td>
                <td>Show synchronization status</td>
                <td>Idem, see above</td>
            </tr>
            <tr className="table-group-divider">
                <td colSpan={3} className="table-dark text-center">
                    <h4 style={{marginBottom: 0}}>Stability</h4>
                </td>
            </tr>
            <tr className="table-info">
                <td>-</td>
                <td>Stability</td>
                <td>As we learn more about, we will improve stability.</td>
            </tr>
            <tr className="table-group-divider">
                <td colSpan={3} className="table-dark text-center">
                    <h4 style={{marginBottom: 0}}>Synchronization options</h4>
                </td>
            </tr>
            <tr className="table-success">
                <td>yes</td>
                <td>Only sync pages with notes</td>
                <td>An e-book can have 400 pages, where only 17 of them contain annotations. With this option, only
                    those 17 pages will show up in your vault.
                </td>
            </tr>
            <tr className="table-warning">
                <td>no</td>
                <td>Sync all pages</td>
                <td>The opposite of the previous option, include all pages every time.</td>
            </tr>
            </tbody>
        </table>
        <h2>Contact us</h2>
        <p>Got feedback? Something missing?</p>
        <span>smg@smgmusicdisplay.com</span>
    </div>
}
