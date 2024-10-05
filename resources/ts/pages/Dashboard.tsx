import React, { useEffect } from 'react'
import { useOnboardingStateQuery } from '../store/api/apiRoot.ts'
import RMFileTree from '../components/feature/RMFileTree/RMFileTree.tsx'
import RMOneTimeCodeCard from '../components/feature/RMOneTimeCodeCard/RMOneTimeCodeCard.tsx'
import { useNavigate } from 'react-router-dom'
import GumroadLicenseCard from '../components/feature/GumroadLicenseCard/GumroadLicenseCard.tsx'
import { cond, constant, isEqual } from 'lodash-es'

export default function Dashboard() {
  const {
    data: onboardingState,
    isError,
    isLoading,
  } = useOnboardingStateQuery()
  const navigate = useNavigate()

  const renderState = cond([
    [
      (state: string) => isEqual('setup-gumroad', state),
      constant(() => (
        <div className="page-centering-container">
          <GumroadLicenseCard />
        </div>
      )),
    ],
    [
      (state: string) => isEqual('setup-one-time-code', state),
      constant(() => (
        <div className="page-centering-container">
          <RMOneTimeCodeCard firstTime />
        </div>
      )),
    ],
    [
      (state: string) => isEqual('setup-one-time-code-again', state),
      constant(() => (
        <div className="page-centering-container">
          <RMOneTimeCodeCard firstTime={false} />
        </div>
      )),
    ],
    [(state: string) => isEqual('ready', state), constant(RMFileTree)],
  ])

  const Component = renderState(onboardingState)

  useEffect(() => {
    if (isError) {
      navigate('/auth/login')
    }
  }, [isError])

  useEffect(() => {
    if (isError) {
      navigate('/')
    }
  }, [isError])

  return <div>{isLoading ? 'loading' : isError ? null : <Component />}</div>
}
