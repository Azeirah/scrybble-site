import React, {useEffect} from "react";
import {useOnboardingStateQuery} from "../store/api/apiRoot";
import _ from "lodash";
import GumroadLicenseCard from "../components/feature/GumroadLicenseCard/GumroadLicenseCard";
import RMFileTree from "../components/feature/RMFileTree";
import RMOneTimeCodeCard from "../components/feature/RMOneTimeCodeCard/RMOneTimeCodeCard";
import {useNavigate} from "react-router-dom";


export default function Dashboard() {
    const {data: onboardingState, isError, isLoading} = useOnboardingStateQuery();
    const navigate = useNavigate();

    const renderState = _.cond([
        [_.matches("setup-gumroad"), _.constant(GumroadLicenseCard)],
        [_.matches("setup-one-time-code"), _.constant(RMOneTimeCodeCard)],
        [_.matches("ready"), _.constant(RMFileTree)],
    ]);

    const Component = renderState(onboardingState);

    useEffect(() => {
        if (isError) {
            navigate('/')
        }
    }, [isError]);

    return <div className="page-centering-container">
        {isLoading ? "loading" : <div><Component/></div>}
    </div>
}
