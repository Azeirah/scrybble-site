import React, {useEffect, useRef} from "react";
import "./dialog.scss"

export function Dialog({open, close, children, title}: { open: boolean, close: () => void, title: string, children: JSX.Element }) {
  const ref = useRef<HTMLDialogElement | null>(null);

  useEffect(() => {
    if (open) {
      ref.current?.showModal()
    } else {
      ref.current?.close()
    }
  }, [open])

  return <dialog ref={ref} onCancel={close} className="bg-dark text-light">
    <div className="modal-content">
      <div className="modal-header">
        <h5 className="modal-title">{title}</h5>
      </div>
      <div className="modal-body">
        {children}
      </div>
      <div className="modal-footer">
        <button onClick={close} className="btn btn-primary">
          Close
        </button>
      </div>
    </div>
  </dialog>
}
