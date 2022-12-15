import React, {useEffect} from "react"
import {useOnboardingStateQuery} from "../store/api/apiRoot"
import _ from "lodash"
import RMFileTree from "../components/feature/RMFileTree/RMFileTree"
import RMOneTimeCodeCard from "../components/feature/RMOneTimeCodeCard/RMOneTimeCodeCard"
import {useNavigate} from "react-router-dom"
import GumroadLicenseCard from "../components/feature/GumroadLicenseCard/GumroadLicenseCard"


export default function Dashboard() {
    const {data: onboardingState, isError, isLoading} = useOnboardingStateQuery()
    const navigate = useNavigate()

    const renderState = _.cond([
        [_.matches("setup-gumroad"), _.constant(
            () => <div className="page-centering-container"><GumroadLicenseCard/></div>)],
        [_.matches("setup-one-time-code"), _.constant(
            () => <div className="page-centering-container"><RMOneTimeCodeCard/></div>)],
        [_.matches("ready"), _.constant(RMFileTree)]
    ])

    const Component = renderState(onboardingState)

    useEffect(() => {
        if (isError) {
            navigate("/auth/login")
        }
    }, [isError])

    useEffect(() => {
        if (isError) {
            navigate("/")
        }
    }, [isError])

    return <div>
        {isLoading ? "loading" : isError ? null : <Component/>}
    </div>
}
